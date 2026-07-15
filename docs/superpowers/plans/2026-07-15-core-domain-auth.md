# Core Domain & Auth Foundation Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build the shared data model, roles, and auth foundation (Fortify dashboard sessions + Sanctum participant API tokens + database/FCM notifications) that every other competition-platform sub-project depends on.

**Architecture:** Plain Eloquent models with PHP backed enums for status/role/kind fields, one migration per table, `spatie/laravel-medialibrary` for submission files, Sanctum for participant API auth (installed alongside the existing Fortify session auth), and Laravel's `database` notification channel plus a custom `fcm` channel.

**Tech Stack:** Laravel 13, PHP 8.4, Pest 4, Sanctum, spatie/laravel-medialibrary, Fortify (existing).

**Spec:** `docs/superpowers/specs/2026-07-15-core-domain-auth-design.md`

---

## Task 1: `Role` enum + `role` column on `users`

**Files:**
- Create: `app/Enums/Role.php`
- Create: `database/migrations/2026_07_15_000001_add_role_to_users_table.php`
- Modify: `app/Models/User.php`
- Modify: `database/factories/UserFactory.php`
- Test: `tests/Unit/Enums/RoleTest.php`
- Test: `tests/Feature/Models/UserRoleTest.php`

- [ ] **Step 1: Write the failing enum test**

```php
<?php

use App\Enums\Role;

it('has the four expected role cases', function () {
    expect(array_column(Role::cases(), 'value'))
        ->toBe(['admin', 'organizer', 'judge', 'participant']);
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact tests/Unit/Enums/RoleTest.php`
Expected: FAIL with `Class "App\Enums\Role" not found`

- [ ] **Step 3: Create the enum**

```php
<?php

namespace App\Enums;

enum Role: string
{
    case Admin = 'admin';
    case Organizer = 'organizer';
    case Judge = 'judge';
    case Participant = 'participant';
}
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --compact tests/Unit/Enums/RoleTest.php`
Expected: PASS

- [ ] **Step 5: Create the migration**

```bash
php artisan make:migration add_role_to_users_table --no-interaction
```

Replace the generated file's contents with:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('participant')->after('email');
            $table->boolean('can_manage_judges')->default(false)->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'can_manage_judges']);
        });
    }
};
```

Rename the file so its timestamp prefix is `2026_07_15_000001` (matching the Files list above) if `make:migration` generated a different timestamp.

- [ ] **Step 6: Add the cast and fillable/factory support to `User`**

In `app/Models/User.php`, add the import and update `casts()`:

```php
use App\Enums\Role;
```

```php
#[Fillable(['name', 'email', 'password', 'role', 'can_manage_judges'])]
```

```php
protected function casts(): array
{
    return [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'role' => Role::class,
    ];
}
```

- [ ] **Step 7: Add role states to `UserFactory`**

Add to `database/factories/UserFactory.php`, after the `unverified()` method:

```php
use App\Enums\Role;
```

```php
public function admin(): static
{
    return $this->state(fn (array $attributes) => ['role' => Role::Admin]);
}

public function organizer(): static
{
    return $this->state(fn (array $attributes) => ['role' => Role::Organizer]);
}

public function judge(): static
{
    return $this->state(fn (array $attributes) => ['role' => Role::Judge]);
}

public function participant(): static
{
    return $this->state(fn (array $attributes) => ['role' => Role::Participant]);
}
```

Also add `'role' => Role::Participant,` to the `definition()` array so factory-made users default to `participant`.

- [ ] **Step 8: Write the failing feature test for the cast**

```php
<?php

use App\Enums\Role;
use App\Models\User;

it('casts role to the Role enum and defaults new users to participant', function () {
    $user = User::factory()->create();

    expect($user->role)->toBe(Role::Participant);

    $organizer = User::factory()->organizer()->create();

    expect($organizer->role)->toBe(Role::Organizer);
});
```

- [ ] **Step 9: Run migrations and tests**

Run: `php artisan migrate --no-interaction && php artisan test --compact tests/Feature/Models/UserRoleTest.php`
Expected: PASS

- [ ] **Step 10: Commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Enums/Role.php app/Models/User.php database/factories/UserFactory.php database/migrations/*add_role_to_users_table.php tests/Unit/Enums/RoleTest.php tests/Feature/Models/UserRoleTest.php
git commit -m "feat: add Role enum and role column to users"
```

---

## Task 2: `CompetitionType` model

**Files:**
- Create: `database/migrations/2026_07_15_000002_create_competition_types_table.php`
- Create: `app/Enums/SubmissionKind.php`
- Create: `app/Models/CompetitionType.php`
- Create: `database/factories/CompetitionTypeFactory.php`
- Test: `tests/Feature/Models/CompetitionTypeTest.php`

- [ ] **Step 1: Write the failing test**

```php
<?php

use App\Enums\SubmissionKind;
use App\Models\CompetitionType;

it('creates a competition type with a submission kind', function () {
    $type = CompetitionType::factory()->create([
        'name' => 'Photography',
        'submission_kind' => SubmissionKind::Image,
    ]);

    expect($type->name)->toBe('Photography')
        ->and($type->slug)->not->toBeEmpty()
        ->and($type->submission_kind)->toBe(SubmissionKind::Image);
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact tests/Feature/Models/CompetitionTypeTest.php`
Expected: FAIL with `Class "App\Models\CompetitionType" not found`

- [ ] **Step 3: Create the `SubmissionKind` enum**

```php
<?php

namespace App\Enums;

enum SubmissionKind: string
{
    case Image = 'image';
    case Pdf = 'pdf';
    case Video = 'video';
    case Text = 'text';
    case Link = 'link';
}
```

- [ ] **Step 4: Create the migration**

```bash
php artisan make:migration create_competition_types_table --no-interaction
```

Replace contents:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competition_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('submission_kind');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competition_types');
    }
};
```

Rename to timestamp prefix `2026_07_15_000002`.

- [ ] **Step 5: Create the model**

```php
<?php

namespace App\Models;

use App\Enums\SubmissionKind;
use Database\Factories\CompetitionTypeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompetitionType extends Model
{
    /** @use HasFactory<CompetitionTypeFactory> */
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description', 'submission_kind'];

    protected function casts(): array
    {
        return [
            'submission_kind' => SubmissionKind::class,
        ];
    }

    /**
     * @return HasMany<Competition, $this>
     */
    public function competitions(): HasMany
    {
        return $this->hasMany(Competition::class);
    }
}
```

- [ ] **Step 6: Create the factory**

```php
<?php

namespace Database\Factories;

use App\Enums\SubmissionKind;
use App\Models\CompetitionType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<CompetitionType>
 */
class CompetitionTypeFactory extends Factory
{
    protected $model = CompetitionType::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->sentence(),
            'submission_kind' => fake()->randomElement(SubmissionKind::cases()),
        ];
    }
}
```

- [ ] **Step 7: Run test to verify it passes**

Run: `php artisan migrate --no-interaction && php artisan test --compact tests/Feature/Models/CompetitionTypeTest.php`
Expected: PASS

- [ ] **Step 8: Commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Enums/SubmissionKind.php app/Models/CompetitionType.php database/factories/CompetitionTypeFactory.php database/migrations/*create_competition_types_table.php tests/Feature/Models/CompetitionTypeTest.php
git commit -m "feat: add CompetitionType model"
```

---

## Task 3: `Competition` model

**Files:**
- Create: `database/migrations/2026_07_15_000003_create_competitions_table.php`
- Create: `app/Enums/CompetitionStatus.php`
- Create: `app/Models/Competition.php`
- Create: `database/factories/CompetitionFactory.php`
- Modify: `app/Models/User.php`
- Test: `tests/Feature/Models/CompetitionTest.php`

- [ ] **Step 1: Write the failing test**

```php
<?php

use App\Enums\CompetitionStatus;
use App\Enums\Role;
use App\Models\Competition;
use App\Models\User;

it('belongs to an organizer and a competition type', function () {
    $organizer = User::factory()->organizer()->create();

    $competition = Competition::factory()->create([
        'organizer_id' => $organizer->id,
        'status' => CompetitionStatus::Open,
    ]);

    expect($competition->organizer->is($organizer))->toBeTrue()
        ->and($competition->competitionType)->not->toBeNull()
        ->and($competition->status)->toBe(CompetitionStatus::Open)
        ->and($organizer->competitions)->toHaveCount(1);
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact tests/Feature/Models/CompetitionTest.php`
Expected: FAIL with `Class "App\Models\Competition" not found`

- [ ] **Step 3: Create the `CompetitionStatus` enum**

```php
<?php

namespace App\Enums;

enum CompetitionStatus: string
{
    case Upcoming = 'upcoming';
    case Open = 'open';
    case Closed = 'closed';
    case UnderEvaluation = 'under_evaluation';
    case Finished = 'finished';
}
```

- [ ] **Step 4: Create the migration**

```bash
php artisan make:migration create_competitions_table --no-interaction
```

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competitions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organizer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('competition_type_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('terms')->nullable();
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->string('status')->default('upcoming');
            $table->boolean('requires_approval')->default(true);
            $table->string('evaluation_method')->default('average_score');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competitions');
    }
};
```

Rename to timestamp prefix `2026_07_15_000003`.

- [ ] **Step 5: Create the model**

```php
<?php

namespace App\Models;

use App\Enums\CompetitionStatus;
use Database\Factories\CompetitionFactory;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Competition extends Model
{
    /** @use HasFactory<CompetitionFactory> */
    use HasFactory;

    protected $fillable = [
        'organizer_id', 'competition_type_id', 'title', 'description', 'terms',
        'starts_at', 'ends_at', 'status', 'requires_approval', 'evaluation_method',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'status' => CompetitionStatus::class,
            'requires_approval' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    /**
     * @return BelongsTo<CompetitionType, $this>
     */
    public function competitionType(): BelongsTo
    {
        return $this->belongsTo(CompetitionType::class);
    }

    /**
     * @return HasMany<Prize, $this>
     */
    public function prizes(): HasMany
    {
        return $this->hasMany(Prize::class);
    }

    /**
     * @return HasMany<Submission, $this>
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    /**
     * @return BelongsToMany<User, $this>
     */
    public function judges(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'competition_judge', 'competition_id', 'judge_id');
    }
}
```

- [ ] **Step 6: Create the factory**

```php
<?php

namespace Database\Factories;

use App\Enums\CompetitionStatus;
use App\Models\Competition;
use App\Models\CompetitionType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Competition>
 */
class CompetitionFactory extends Factory
{
    protected $model = Competition::class;

    public function definition(): array
    {
        $startsAt = fake()->dateTimeBetween('now', '+1 month');

        return [
            'organizer_id' => User::factory()->organizer(),
            'competition_type_id' => CompetitionType::factory(),
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'terms' => fake()->paragraph(),
            'starts_at' => $startsAt,
            'ends_at' => fake()->dateTimeBetween($startsAt, '+2 months'),
            'status' => CompetitionStatus::Upcoming,
            'requires_approval' => true,
            'evaluation_method' => 'average_score',
        ];
    }
}
```

- [ ] **Step 7: Add the inverse relationship to `User`**

Add to `app/Models/User.php`:

```php
use Illuminate\Database\Eloquent\Relations\HasMany;
```

```php
/**
 * @return HasMany<Competition, $this>
 */
public function competitions(): HasMany
{
    return $this->hasMany(Competition::class, 'organizer_id');
}
```

- [ ] **Step 8: Run test to verify it passes**

Run: `php artisan migrate --no-interaction && php artisan test --compact tests/Feature/Models/CompetitionTest.php`
Expected: PASS

- [ ] **Step 9: Commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Enums/CompetitionStatus.php app/Models/Competition.php app/Models/User.php database/factories/CompetitionFactory.php database/migrations/*create_competitions_table.php tests/Feature/Models/CompetitionTest.php
git commit -m "feat: add Competition model"
```

---

## Task 4: `Prize` model

**Files:**
- Create: `database/migrations/2026_07_15_000004_create_prizes_table.php`
- Create: `app/Models/Prize.php`
- Create: `database/factories/PrizeFactory.php`
- Test: `tests/Feature/Models/PrizeTest.php`

- [ ] **Step 1: Write the failing test**

```php
<?php

use App\Models\Competition;
use App\Models\Prize;

it('belongs to a competition', function () {
    $competition = Competition::factory()->create();

    $prize = Prize::factory()->create([
        'competition_id' => $competition->id,
        'rank' => 1,
        'winners_count' => 1,
    ]);

    expect($prize->competition->is($competition))->toBeTrue()
        ->and($competition->prizes)->toHaveCount(1)
        ->and($prize->rank)->toBe(1);
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact tests/Feature/Models/PrizeTest.php`
Expected: FAIL with `Class "App\Models\Prize" not found`

- [ ] **Step 3: Create the migration**

```bash
php artisan make:migration create_prizes_table --no-interaction
```

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prizes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedInteger('winners_count')->default(1);
            $table->unsignedInteger('rank');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prizes');
    }
};
```

Rename to timestamp prefix `2026_07_15_000004`.

- [ ] **Step 4: Create the model**

```php
<?php

namespace App\Models;

use Database\Factories\PrizeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prize extends Model
{
    /** @use HasFactory<PrizeFactory> */
    use HasFactory;

    protected $fillable = ['competition_id', 'title', 'description', 'winners_count', 'rank'];

    /**
     * @return BelongsTo<Competition, $this>
     */
    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }
}
```

- [ ] **Step 5: Create the factory**

```php
<?php

namespace Database\Factories;

use App\Models\Competition;
use App\Models\Prize;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Prize>
 */
class PrizeFactory extends Factory
{
    protected $model = Prize::class;

    public function definition(): array
    {
        return [
            'competition_id' => Competition::factory(),
            'title' => fake()->words(2, true),
            'description' => fake()->sentence(),
            'winners_count' => 1,
            'rank' => fake()->numberBetween(1, 3),
        ];
    }
}
```

- [ ] **Step 6: Run test to verify it passes**

Run: `php artisan migrate --no-interaction && php artisan test --compact tests/Feature/Models/PrizeTest.php`
Expected: PASS

- [ ] **Step 7: Commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Models/Prize.php database/factories/PrizeFactory.php database/migrations/*create_prizes_table.php tests/Feature/Models/PrizeTest.php
git commit -m "feat: add Prize model"
```

---

## Task 5: Install `spatie/laravel-medialibrary` + `Submission` model

**Files:**
- Modify: `composer.json` (via composer require)
- Create: `database/migrations/2026_07_15_000005_create_submissions_table.php`
- Create: `app/Enums/SubmissionStatus.php`
- Create: `app/Models/Submission.php`
- Create: `database/factories/SubmissionFactory.php`
- Modify: `app/Models/User.php`
- Test: `tests/Feature/Models/SubmissionTest.php`

- [ ] **Step 1: Install the package**

```bash
composer require spatie/laravel-medialibrary --no-interaction
php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="medialibrary-migrations" --no-interaction
php artisan migrate --no-interaction
```

This publishes and runs Spatie's own `create_media_table` migration — do not hand-write it.

- [ ] **Step 2: Write the failing test**

```php
<?php

use App\Enums\SubmissionStatus;
use App\Models\Competition;
use App\Models\Submission;
use App\Models\User;

it('belongs to a competition and a participant, and accepts text content', function () {
    $competition = Competition::factory()->create();
    $participant = User::factory()->participant()->create();

    $submission = Submission::factory()->create([
        'competition_id' => $competition->id,
        'participant_id' => $participant->id,
        'text_content' => 'My entry text',
        'status' => SubmissionStatus::Submitted,
    ]);

    expect($submission->competition->is($competition))->toBeTrue()
        ->and($submission->participant->is($participant))->toBeTrue()
        ->and($submission->text_content)->toBe('My entry text')
        ->and($submission->status)->toBe(SubmissionStatus::Submitted)
        ->and($competition->submissions)->toHaveCount(1)
        ->and($participant->submissions)->toHaveCount(1);
});

it('can attach a media file to a submission', function () {
    $submission = Submission::factory()->create();

    $submission->addMediaFromString('fake file contents')
        ->usingFileName('entry.jpg')
        ->toMediaCollection('submission_files');

    expect($submission->getMedia('submission_files'))->toHaveCount(1);
});
```

- [ ] **Step 3: Run test to verify it fails**

Run: `php artisan test --compact tests/Feature/Models/SubmissionTest.php`
Expected: FAIL with `Class "App\Models\Submission" not found`

- [ ] **Step 4: Create the `SubmissionStatus` enum**

```php
<?php

namespace App\Enums;

enum SubmissionStatus: string
{
    case Submitted = 'submitted';
    case UnderReview = 'under_review';
    case Accepted = 'accepted';
    case Rejected = 'rejected';
    case UnderEvaluation = 'under_evaluation';
    case Evaluated = 'evaluated';
}
```

- [ ] **Step 5: Create the migration**

```bash
php artisan make:migration create_submissions_table --no-interaction
```

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_id')->constrained()->cascadeOnDelete();
            $table->foreignId('participant_id')->constrained('users')->cascadeOnDelete();
            $table->string('status')->default('submitted');
            $table->text('text_content')->nullable();
            $table->string('link_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
```

Rename to timestamp prefix `2026_07_15_000005` (it must run after the medialibrary migration published in Step 1, which will already have an earlier `2026_07_15` timestamp from `vendor:publish` — check with `ls database/migrations` and adjust this file's prefix to sort after it if needed).

- [ ] **Step 6: Create the model**

```php
<?php

namespace App\Models;

use App\Enums\SubmissionStatus;
use Database\Factories\SubmissionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Submission extends Model implements HasMedia
{
    /** @use HasFactory<SubmissionFactory> */
    use HasFactory, InteractsWithMedia;

    protected $fillable = ['competition_id', 'participant_id', 'status', 'text_content', 'link_url'];

    protected function casts(): array
    {
        return [
            'status' => SubmissionStatus::class,
        ];
    }

    /**
     * @return BelongsTo<Competition, $this>
     */
    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function participant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'participant_id');
    }

    /**
     * @return HasMany<Evaluation, $this>
     */
    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class);
    }
}
```

- [ ] **Step 7: Create the factory**

```php
<?php

namespace Database\Factories;

use App\Enums\SubmissionStatus;
use App\Models\Competition;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Submission>
 */
class SubmissionFactory extends Factory
{
    protected $model = Submission::class;

    public function definition(): array
    {
        return [
            'competition_id' => Competition::factory(),
            'participant_id' => User::factory()->participant(),
            'status' => SubmissionStatus::Submitted,
            'text_content' => fake()->paragraph(),
            'link_url' => null,
        ];
    }
}
```

- [ ] **Step 8: Add the inverse relationship to `User`**

Add to `app/Models/User.php`:

```php
/**
 * @return HasMany<Submission, $this>
 */
public function submissions(): HasMany
{
    return $this->hasMany(Submission::class, 'participant_id');
}
```

- [ ] **Step 9: Run test to verify it passes**

Run: `php artisan test --compact tests/Feature/Models/SubmissionTest.php`
Expected: PASS

- [ ] **Step 10: Commit**

```bash
vendor/bin/pint --dirty --format agent
git add composer.json composer.lock app/Enums/SubmissionStatus.php app/Models/Submission.php app/Models/User.php database/factories/SubmissionFactory.php database/migrations/*create_submissions_table.php database/migrations/*create_media_table.php tests/Feature/Models/SubmissionTest.php
git commit -m "feat: add Submission model with media library support"
```

---

## Task 6: `competition_judge` pivot

**Files:**
- Create: `database/migrations/2026_07_15_000006_create_competition_judge_table.php`
- Modify: `app/Models/User.php`
- Test: `tests/Feature/Models/CompetitionJudgeTest.php`

- [ ] **Step 1: Write the failing test**

```php
<?php

use App\Models\Competition;
use App\Models\User;

it('assigns a judge to a competition', function () {
    $competition = Competition::factory()->create();
    $judge = User::factory()->judge()->create();

    $competition->judges()->attach($judge);

    expect($competition->judges)->toHaveCount(1)
        ->and($competition->judges->first()->is($judge))->toBeTrue()
        ->and($judge->judgedCompetitions)->toHaveCount(1);
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact tests/Feature/Models/CompetitionJudgeTest.php`
Expected: FAIL — `judgedCompetitions` undefined method, or table missing error

- [ ] **Step 3: Create the migration**

```bash
php artisan make:migration create_competition_judge_table --no-interaction
```

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competition_judge', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_id')->constrained()->cascadeOnDelete();
            $table->foreignId('judge_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['competition_id', 'judge_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competition_judge');
    }
};
```

Rename to timestamp prefix `2026_07_15_000006`.

- [ ] **Step 4: Add the inverse relationship to `User`**

Add to `app/Models/User.php`:

```php
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
```

```php
/**
 * @return BelongsToMany<Competition, $this>
 */
public function judgedCompetitions(): BelongsToMany
{
    return $this->belongsToMany(Competition::class, 'competition_judge', 'judge_id', 'competition_id');
}
```

- [ ] **Step 5: Run test to verify it passes**

Run: `php artisan migrate --no-interaction && php artisan test --compact tests/Feature/Models/CompetitionJudgeTest.php`
Expected: PASS

- [ ] **Step 6: Commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Models/User.php database/migrations/*create_competition_judge_table.php tests/Feature/Models/CompetitionJudgeTest.php
git commit -m "feat: add competition_judge pivot"
```

---

## Task 7: `Evaluation` model + average score aggregation

**Files:**
- Create: `database/migrations/2026_07_15_000007_create_evaluations_table.php`
- Create: `app/Enums/EvaluationStatus.php`
- Create: `app/Models/Evaluation.php`
- Create: `database/factories/EvaluationFactory.php`
- Modify: `app/Models/Submission.php`
- Modify: `app/Models/User.php`
- Test: `tests/Feature/Models/EvaluationTest.php`

- [ ] **Step 1: Write the failing test**

```php
<?php

use App\Enums\EvaluationStatus;
use App\Models\Evaluation;
use App\Models\Submission;
use App\Models\User;

it('records one evaluation per judge per submission and averages the score', function () {
    $submission = Submission::factory()->create();
    $judgeOne = User::factory()->judge()->create();
    $judgeTwo = User::factory()->judge()->create();

    Evaluation::factory()->create([
        'submission_id' => $submission->id,
        'judge_id' => $judgeOne->id,
        'score' => 80,
        'status' => EvaluationStatus::Evaluated,
    ]);

    Evaluation::factory()->create([
        'submission_id' => $submission->id,
        'judge_id' => $judgeTwo->id,
        'score' => 90,
        'status' => EvaluationStatus::Evaluated,
    ]);

    expect($submission->evaluations)->toHaveCount(2)
        ->and($submission->averageScore())->toBe(85.0);
});

it('rejects a second evaluation from the same judge for the same submission', function () {
    $submission = Submission::factory()->create();
    $judge = User::factory()->judge()->create();

    Evaluation::factory()->create(['submission_id' => $submission->id, 'judge_id' => $judge->id]);

    Evaluation::factory()->create(['submission_id' => $submission->id, 'judge_id' => $judge->id]);
})->throws(\Illuminate\Database\QueryException::class);
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact tests/Feature/Models/EvaluationTest.php`
Expected: FAIL with `Class "App\Models\Evaluation" not found`

- [ ] **Step 3: Create the `EvaluationStatus` enum**

```php
<?php

namespace App\Enums;

enum EvaluationStatus: string
{
    case Pending = 'pending';
    case Evaluated = 'evaluated';
    case NeedsReview = 'needs_review';
}
```

- [ ] **Step 4: Create the migration**

```bash
php artisan make:migration create_evaluations_table --no-interaction
```

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('judge_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedInteger('score')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
            $table->unique(['submission_id', 'judge_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};
```

Rename to timestamp prefix `2026_07_15_000007`.

- [ ] **Step 5: Create the model**

```php
<?php

namespace App\Models;

use App\Enums\EvaluationStatus;
use Database\Factories\EvaluationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Evaluation extends Model
{
    /** @use HasFactory<EvaluationFactory> */
    use HasFactory;

    protected $fillable = ['submission_id', 'judge_id', 'score', 'notes', 'status'];

    protected function casts(): array
    {
        return [
            'status' => EvaluationStatus::class,
        ];
    }

    /**
     * @return BelongsTo<Submission, $this>
     */
    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function judge(): BelongsTo
    {
        return $this->belongsTo(User::class, 'judge_id');
    }
}
```

- [ ] **Step 6: Create the factory**

```php
<?php

namespace Database\Factories;

use App\Enums\EvaluationStatus;
use App\Models\Evaluation;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Evaluation>
 */
class EvaluationFactory extends Factory
{
    protected $model = Evaluation::class;

    public function definition(): array
    {
        return [
            'submission_id' => Submission::factory(),
            'judge_id' => User::factory()->judge(),
            'score' => fake()->numberBetween(0, 100),
            'notes' => fake()->sentence(),
            'status' => EvaluationStatus::Evaluated,
        ];
    }
}
```

- [ ] **Step 7: Add `evaluations()` relation (already added in Task 5) and `averageScore()` to `Submission`**

Add to `app/Models/Submission.php`:

```php
public function averageScore(): ?float
{
    $average = $this->evaluations()->whereNotNull('score')->avg('score');

    return $average === null ? null : (float) $average;
}
```

- [ ] **Step 8: Add the inverse relationship to `User`**

Add to `app/Models/User.php`:

```php
/**
 * @return HasMany<Evaluation, $this>
 */
public function evaluations(): HasMany
{
    return $this->hasMany(Evaluation::class, 'judge_id');
}
```

- [ ] **Step 9: Run test to verify it passes**

Run: `php artisan migrate --no-interaction && php artisan test --compact tests/Feature/Models/EvaluationTest.php`
Expected: PASS

- [ ] **Step 10: Commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Enums/EvaluationStatus.php app/Models/Evaluation.php app/Models/Submission.php app/Models/User.php database/factories/EvaluationFactory.php database/migrations/*create_evaluations_table.php tests/Feature/Models/EvaluationTest.php
git commit -m "feat: add Evaluation model with average score aggregation"
```

---

## Task 8: Role-based dashboard middleware

**Files:**
- Create: `app/Http/Middleware/EnsureUserHasRole.php`
- Modify: `bootstrap/app.php`
- Test: `tests/Feature/Middleware/EnsureUserHasRoleTest.php`

- [ ] **Step 1: Write the failing test**

```php
<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    Route::middleware(['web', 'auth', 'role:admin,organizer'])
        ->get('/test-role-route', fn () => 'ok');
});

it('allows a user whose role is in the allowed list', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->get('/test-role-route')->assertOk();
});

it('forbids a user whose role is not in the allowed list', function () {
    $participant = User::factory()->participant()->create();

    $this->actingAs($participant)->get('/test-role-route')->assertForbidden();
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact tests/Feature/Middleware/EnsureUserHasRoleTest.php`
Expected: FAIL — `role` middleware alias not registered (500 or route resolution error)

- [ ] **Step 3: Create the middleware**

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        abort_unless($user && in_array($user->role->value, $roles, true), 403);

        return $next($request);
    }
}
```

- [ ] **Step 4: Register the `role` middleware alias**

In `bootstrap/app.php`, update the `withMiddleware` closure:

```php
use App\Http\Middleware\EnsureUserHasRole;
```

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->encryptCookies(except: ['appearance', 'sidebar_state']);

    $middleware->web(append: [
        HandleAppearance::class,
        HandleInertiaRequests::class,
        AddLinkHeadersForPreloadedAssets::class,
    ]);

    $middleware->alias([
        'role' => EnsureUserHasRole::class,
    ]);
})
```

- [ ] **Step 5: Run test to verify it passes**

Run: `php artisan test --compact tests/Feature/Middleware/EnsureUserHasRoleTest.php`
Expected: PASS

- [ ] **Step 6: Commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Http/Middleware/EnsureUserHasRole.php bootstrap/app.php tests/Feature/Middleware/EnsureUserHasRoleTest.php
git commit -m "feat: add role-based dashboard middleware"
```

---

## Task 9: Sanctum participant API auth

**Files:**
- Modify: `bootstrap/app.php`
- Create: `routes/api.php` (generated by `install:api`, then edited)
- Create: `app/Http/Controllers/Api/AuthController.php`
- Test: `tests/Feature/Api/ParticipantAuthTest.php`

- [ ] **Step 1: Install Sanctum and API routing**

```bash
php artisan install:api --no-interaction
```

This adds `laravel/sanctum` to `composer.json`, publishes its migration, creates `routes/api.php`, and wires `api:` routing into `bootstrap/app.php` automatically. Run:

```bash
php artisan migrate --no-interaction
```

- [ ] **Step 2: Write the failing test**

```php
<?php

use App\Enums\Role;
use App\Models\User;

it('registers a participant and returns a token', function () {
    $response = $this->postJson('/api/register', [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertCreated()->assertJsonStructure(['token', 'user' => ['id', 'name', 'email', 'role']]);

    $user = User::where('email', 'jane@example.com')->firstOrFail();
    expect($user->role)->toBe(Role::Participant);
});

it('logs a participant in and returns a token', function () {
    $user = User::factory()->participant()->create(['password' => bcrypt('password')]);

    $response = $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertOk()->assertJsonStructure(['token', 'user' => ['id', 'name', 'email', 'role']]);
});

it('rejects login with a bad password', function () {
    $user = User::factory()->participant()->create(['password' => bcrypt('password')]);

    $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ])->assertUnauthorized();
});

it('logs the participant out and revokes the token', function () {
    $user = User::factory()->participant()->create();
    $token = $user->createToken('flutter-app')->plainTextToken;

    $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/logout')
        ->assertNoContent();

    expect($user->tokens()->count())->toBe(0);
});
```

- [ ] **Step 3: Run test to verify it fails**

Run: `php artisan test --compact tests/Feature/Api/ParticipantAuthTest.php`
Expected: FAIL — 404s, routes not defined

- [ ] **Step 4: Add `HasApiTokens` to `User`**

In `app/Models/User.php`:

```php
use Laravel\Sanctum\HasApiTokens;
```

```php
use HasApiTokens, HasFactory, Notifiable, PasskeyAuthenticatable;
```

- [ ] **Step 5: Create the controller**

```php
<?php

namespace App\Http\Controllers\Api;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => Role::Participant,
        ]);

        return response()->json([
            'token' => $user->createToken('flutter-app')->plainTextToken,
            'user' => $user->only(['id', 'name', 'email', 'role']),
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ])->status(401);
        }

        return response()->json([
            'token' => $user->createToken('flutter-app')->plainTextToken,
            'user' => $user->only(['id', 'name', 'email', 'role']),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(null, 204);
    }
}
```

- [ ] **Step 6: Add the routes**

In `routes/api.php` (append after whatever `install:api` generated):

```php
use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});
```

- [ ] **Step 7: Run test to verify it passes**

Run: `php artisan test --compact tests/Feature/Api/ParticipantAuthTest.php`
Expected: PASS

- [ ] **Step 8: Commit**

```bash
vendor/bin/pint --dirty --format agent
git add composer.json composer.lock bootstrap/app.php routes/api.php app/Http/Controllers/Api/AuthController.php app/Models/User.php database/migrations/*create_personal_access_tokens_table.php tests/Feature/Api/ParticipantAuthTest.php
git commit -m "feat: add Sanctum participant registration, login, and logout"
```

---

## Task 10: Database + FCM notification infrastructure

**Files:**
- Create: `database/migrations/2026_07_15_000008_create_notifications_table.php`
- Create: `database/migrations/2026_07_15_000009_add_fcm_token_to_users_table.php`
- Create: `app/Notifications/Channels/FcmChannel.php`
- Create: `app/Notifications/SubmissionStatusChanged.php`
- Modify: `app/Models/User.php`
- Test: `tests/Feature/Notifications/SubmissionStatusChangedTest.php`

- [ ] **Step 1: Install the FCM-sending package and generate the notifications table migration**

```bash
composer require kreait/laravel-firebase --no-interaction
php artisan make:notifications-table --no-interaction
```

`kreait/laravel-firebase` is installed now (per the spec's approved dependency list) but left unconfigured — it has no boot-time credential requirement, so the app runs fine without a `.env` service account key until real push sending is wired up in a later sub-project.

Rename the generated file's timestamp prefix to `2026_07_15_000008` if it differs.

- [ ] **Step 2: Create the `fcm_token` migration**

```bash
php artisan make:migration add_fcm_token_to_users_table --no-interaction
```

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('fcm_token')->nullable()->after('remember_token');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('fcm_token');
        });
    }
};
```

Rename to timestamp prefix `2026_07_15_000009`.

Run: `php artisan migrate --no-interaction`

- [ ] **Step 3: Write the failing test**

```php
<?php

use App\Models\Submission;
use App\Notifications\SubmissionStatusChanged;
use Illuminate\Support\Facades\Notification;

it('sends a database notification when a submission status changes', function () {
    Notification::fake();

    $submission = Submission::factory()->create();

    $submission->participant->notify(new SubmissionStatusChanged($submission));

    Notification::assertSentTo(
        $submission->participant,
        SubmissionStatusChanged::class,
        fn (SubmissionStatusChanged $notification) => $notification->toArray($submission->participant)['submission_id'] === $submission->id
    );
});

it('stores the notification in the database channel', function () {
    $submission = Submission::factory()->create();

    $submission->participant->notify(new SubmissionStatusChanged($submission));

    expect($submission->participant->notifications()->count())->toBe(1);
    expect($submission->participant->notifications()->first()->data['status'])->toBe($submission->status->value);
});
```

- [ ] **Step 4: Run test to verify it fails**

Run: `php artisan test --compact tests/Feature/Notifications/SubmissionStatusChangedTest.php`
Expected: FAIL with `Class "App\Notifications\SubmissionStatusChanged" not found`

- [ ] **Step 5: Create the FCM channel stub**

```php
<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;

class FcmChannel
{
    /**
     * Send the notification via FCM.
     *
     * Wiring to an actual FCM-sending package (e.g. kreait/laravel-firebase)
     * happens once Firebase credentials are supplied; until then this is a
     * no-op if the user has no fcm_token.
     */
    public function send(mixed $notifiable, Notification $notification): void
    {
        $token = $notifiable->fcm_token ?? null;

        if (! $token || ! method_exists($notification, 'toFcm')) {
            return;
        }

        // Actual push send is implemented when Firebase credentials are configured.
    }
}
```

- [ ] **Step 6: Create the notification**

```php
<?php

namespace App\Notifications;

use App\Models\Submission;
use App\Notifications\Channels\FcmChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class SubmissionStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Submission $submission) {}

    /**
     * @return array<int, string|class-string>
     */
    public function via(mixed $notifiable): array
    {
        return ['database', FcmChannel::class];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(mixed $notifiable): array
    {
        return [
            'submission_id' => $this->submission->id,
            'competition_id' => $this->submission->competition_id,
            'status' => $this->submission->status->value,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toFcm(mixed $notifiable): array
    {
        return [
            'title' => 'Submission update',
            'body' => "Your submission is now {$this->submission->status->value}.",
        ];
    }
}
```

- [ ] **Step 7: Add `Notifiable` fillable/hidden coverage for `fcm_token`**

`User` already uses the `Notifiable` trait, so `notifications()` and `notify()` are available. Add `fcm_token` to the `#[Fillable]` attribute list in `app/Models/User.php`:

```php
#[Fillable(['name', 'email', 'password', 'role', 'can_manage_judges', 'fcm_token'])]
```

- [ ] **Step 8: Run test to verify it passes**

Run: `php artisan test --compact tests/Feature/Notifications/SubmissionStatusChangedTest.php`
Expected: PASS

- [ ] **Step 9: Commit**

```bash
vendor/bin/pint --dirty --format agent
git add composer.json composer.lock app/Models/User.php app/Notifications database/migrations/*create_notifications_table.php database/migrations/*add_fcm_token_to_users_table.php tests/Feature/Notifications/SubmissionStatusChangedTest.php
git commit -m "feat: add database and FCM notification channels"
```

---

## Task 11: Full suite check

**Files:** none (verification only)

- [ ] **Step 1: Run the entire test suite**

Run: `php artisan test --compact`
Expected: All tests PASS, including every test added in Tasks 1-10 plus pre-existing auth/settings tests.

- [ ] **Step 2: Run Pint across the whole app once more**

Run: `vendor/bin/pint --format agent`
Expected: No remaining style violations.

- [ ] **Step 3: Run Larastan static analysis**

Run: `vendor/bin/phpstan analyse --no-interaction`
Expected: No new errors introduced by this plan's code (pre-existing baseline errors, if any, are out of scope).

- [ ] **Step 4: Commit any final formatting fixes**

```bash
git add -A
git commit -m "chore: final formatting pass for core domain & auth foundation"
```

(Skip this commit if there is nothing to stage.)
