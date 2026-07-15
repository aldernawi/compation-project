# Core Domain & Auth Foundation — Design Spec

**Date:** 2026-07-15
**Status:** Approved (sub-project 1 of the competition platform)

## Context

The full competition platform has five user types (Admin, Organizer, Judge, Participant, Guest) across two front ends: a Laravel/Inertia dashboard (Admin/Organizer/Judge) and a Flutter mobile app (Participant/Guest, talking only to backend APIs). The system is too large for one spec, so it's decomposed into sub-projects:

1. **Core domain + auth** (this spec) — data model, roles, Fortify + Sanctum auth, notification infrastructure.
2. Admin dashboard
3. Organizer dashboard
4. Judge evaluation workflow
5. Participant/Guest Sanctum API (Flutter backend)

This spec covers only #1 — the shared foundation everything else depends on.

## Roles

Single `users` table with a `role` string/enum column: `admin`, `organizer`, `judge`, `participant`. Guests are simply unauthenticated requests — no `guest` role needed.

Organizer-specific permission grants (e.g. "grant permissions to a specific organizer" from the source spec) are represented as boolean/JSON flag columns on the user row (e.g. `can_manage_judges`) rather than a full permissions package. This avoids adding `spatie/laravel-permission` until real per-organizer permission granularity is needed — YAGNI for phase 1.

## Data Model

### `competition_types`
- `name`, `slug`, `description`
- `submission_kind` enum: `image`, `pdf`, `video`, `text`, `link`
- Admin CRUDs this table; it determines what a participant can upload for a given competition.

### `competitions`
- `organizer_id` (FK users)
- `competition_type_id` (FK competition_types)
- `title`, `description`, `terms`
- `starts_at`, `ends_at`
- `status` enum: `upcoming`, `open`, `closed`, `under_evaluation`, `finished`
- `requires_approval` (bool) — whether a submission needs organizer accept/reject before it counts
- `evaluation_method` (string/enum, e.g. `average_score`)

### `prizes`
- `competition_id` (FK)
- `title`, `description`
- `winners_count`
- `rank` (1st/2nd/3rd place slot)

### `submissions`
- `competition_id` (FK), `participant_id` (FK users)
- `status` enum: `submitted`, `under_review`, `accepted`, `rejected`, `under_evaluation`, `evaluated`
- `text_content` (nullable string) — for text-type competitions
- `link_url` (nullable string) — for link-type competitions
- File-based submissions (image/PDF/video) use **spatie/laravel-medialibrary** (new dependency, approved) instead of raw file columns. The submission's expected content type is implied by its competition's `competition_type.submission_kind`, so no redundant `type` column is needed on `submissions`.

### `competition_judge` (pivot)
- `competition_id`, `judge_id`
- Whole-competition assignment — a judge assigned to a competition sees all of its accepted submissions. Per-submission scoping is out of scope for phase 1 (documented as a future `submission_judge` pivot if needed).

### `evaluations`
- `submission_id` (FK), `judge_id` (FK users)
- `score`, `notes`
- `status` enum: `pending`, `evaluated`, `needs_review`
- Unique constraint on (`submission_id`, `judge_id`) — one evaluation row per judge per submission.
- Final ranking for a submission = average `score` across its `evaluations` rows. Supports multiple judges scoring the same submission independently, per the source spec's "View judges' scores" (plural).

## Auth

- **Fortify** (already installed) handles session-based login/logout/registration for `admin`, `organizer`, and `judge` roles on the Inertia dashboard. Route groups are gated by role-checking middleware.
- **Sanctum** (new dependency, approved) issues personal access tokens for `participant` accounts via dedicated endpoints: `POST /api/register`, `POST /api/login`, `POST /api/logout`. This is the entire auth surface the Flutter app uses.
- Guest-facing API routes (public competitions, public results) require no authentication.

## Notifications

- Laravel's **database** notification channel stores all notifications (submission accepted/rejected, competition ending soon, results published, winner announcement) in a `notifications` table. Exposed to the Flutter app via an authenticated `GET /api/notifications` endpoint, and usable in the dashboard for organizer/admin-triggered notices.
- A custom **fcm** notification channel sends push notifications via Firebase Cloud Messaging (new dependency, approved). Requires:
  - An `fcm_token` column on `users`, set by the Flutter app on login/token refresh.
  - A Firebase service account key supplied via `.env` before pushes can actually send — the code is wired up regardless of whether credentials are present yet.

## New Dependencies (require the approvals already given in this conversation)

- `laravel/sanctum` — participant/guest API auth
- `spatie/laravel-medialibrary` — file-based submission storage
- An FCM-sending package (e.g. `kreait/laravel-firebase`) — push notification channel

## Testing

- Feature tests per model's factory + relationships (competitions, submissions, evaluations, prizes).
- Feature tests for Fortify-gated dashboard routes (role middleware enforcement).
- Feature tests for Sanctum participant auth endpoints (register/login/logout, token issuance).
- Feature test for the average-score evaluation aggregation.

## Out of Scope (future sub-projects)

- Admin dashboard UI/controllers
- Organizer dashboard UI/controllers
- Judge evaluation UI/controllers
- Participant/Guest API endpoints beyond auth (competition listing, submission upload, status tracking, results)
- Per-submission judge scoping
- Reports & statistics
