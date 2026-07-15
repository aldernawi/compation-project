# Shared Dashboard Shell — Design Spec

**Date:** 2026-07-15
**Status:** Approved (sub-project 2 of the competition platform)

## Context

Sub-project 1 (core domain + auth) is complete. The remaining work is three role-based dashboards (Admin, Organizer, Judge) built on Inertia/React, plus the rest of the Flutter-facing Sanctum API. All three dashboards share one Laravel/Inertia app and the existing starter-kit layout (`resources/js/layouts/app-layout.tsx` → `app-sidebar-layout.tsx` → `AppSidebar`), so before building any one dashboard's features, this sub-project establishes the shared foundation they all sit on:

1. **Core domain + auth** — done.
2. **Dashboard shell** (this spec) — role-prefixed routing, role-aware navigation, shared `DataTable` component.
3. Admin dashboard
4. Organizer dashboard
5. Judge dashboard
6. Participant/Guest Sanctum API (rest of the Flutter backend)

This spec covers only #2.

## Routing & Page Structure

- Three new route files, each required from `routes/web.php`:
  - `routes/admin.php` — `Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(...)`
  - `routes/organizer.php` — same pattern with `role:organizer`, prefix/name `organizer`
  - `routes/judge.php` — same pattern with `role:judge`, prefix/name `judge`
- Matching Inertia page folders: `resources/js/pages/admin/`, `resources/js/pages/organizer/`, `resources/js/pages/judge/`.
- Controllers live in `app/Http/Controllers/Admin/`, `app/Http/Controllers/Organizer/`, `app/Http/Controllers/Judge/`. This sub-project adds one `DashboardController` per role, each with a single `index()` method rendering that role's landing page (`Inertia::render('admin/index')`, etc.) — a placeholder page for later specs to build resource management on top of.
- Each landing page just confirms the shell works: renders inside `AppLayout` with a breadcrumb of `[{title: '<Role> Dashboard', href: ...}]` and a simple "Welcome" card.

## Role-Aware Navigation

- `resources/js/components/app-sidebar.tsx` reads `auth.user.role` via `usePage<SharedData>().props` and selects one of three `NavItem[]` arrays: `adminNavItems`, `organizerNavItems`, `judgeNavItems`, replacing the current hardcoded `mainNavItems`.
- Each array starts with a single `{ title: 'Dashboard', href: <role>.dashboard(), icon: LayoutGrid }` entry pointing at that role's landing route. Later specs append resource links (e.g. Admin gets "Users", "Competitions"; Organizer gets "My Competitions", "Submissions"; Judge gets "Assigned Competitions").
- If `auth.user` is null or its role doesn't match any of the three (shouldn't happen given route middleware, but the component must not crash), fall back to an empty nav array.
- The rest of the chrome (logo, footer links, user menu) is unchanged and shared across all roles.

## Shared `DataTable` Component

- New file: `resources/js/components/data-table.tsx`.
- Built on shadcn's `Table` primitive (not yet installed — added via `npx shadcn@latest add table` as part of implementation) plus Inertia's `<Link>` for pagination.
- Props: `columns: DataTableColumn<T>[]` (each with `header: string` and `cell: (row: T) => ReactNode`), and `paginator: LaravelPaginator<T>` matching the shape returned by Eloquent's `->paginate()` serialized through an Inertia prop (`{ data: T[]; links: { url: string | null; label: string; active: boolean }[]; meta: { current_page, last_page, total, ... } }`).
- Renders a `<Table>` with one `<TableRow>` per `paginator.data` item using the provided `columns`, and a pagination footer built from `paginator.links`, each rendered as an Inertia `<Link>` (preserving scroll position, no client-side page state).
- No built-in sorting or search — those are query-string concerns specific to each resource page and are added by later specs when they build a given list view, keeping this component a pure rendering shell rather than growing hidden feature-specific logic.

## Testing

- Feature tests per role verifying: a user with the matching role can access `GET /<role>` (200 + correct Inertia component), and a user with a different role gets 403 (exercising the `role:` middleware from sub-project 1 against the new routes).
- No frontend/JS tests in this sub-project (no test runner is configured yet); the `DataTable` and nav logic are exercised through manual verification and later specs' own feature tests once they render real data through it.

## Out of Scope (future sub-projects)

- Any actual resource management (users, competitions, submissions, prizes, evaluations, reports) — those are Admin/Organizer/Judge specs.
- Sorting, search, or filtering UI for `DataTable` — added per-page as needed.
- The rest of the Participant/Guest Sanctum API.
