# Shared Dashboard Shell Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build the shared foundation (role-prefixed routes/pages, role-aware sidebar navigation, and a reusable `DataTable` component) that the Admin, Organizer, and Judge dashboards will all build on top of.

**Architecture:** Three role-gated route groups (`/admin`, `/organizer`, `/judge`) each with a placeholder `DashboardController@index` rendering a landing Inertia page; a role-aware `AppSidebar` that swaps its nav-items array based on `auth.user.role`; and a generic `DataTable` React component backed by a hand-written shadcn `Table` primitive, rendering any Laravel `->paginate()` result.

**Tech Stack:** Laravel 13, Inertia v3 + React 19, Laravel Wayfinder (typed route helpers), Pest 4, TypeScript, Tailwind v4.

**Spec:** `docs/superpowers/specs/2026-07-15-dashboard-shell-design.md`

---

## Task 1: Shared TypeScript types

**Files:**
- Modify: `resources/js/types/auth.ts`
- Create: `resources/js/types/pagination.ts`
- Modify: `resources/js/types/index.ts`

- [ ] **Step 1: Add `role` to the shared `User` type**

In `resources/js/types/auth.ts`, update the `User` type:

```ts
export type User = {
    id: number;
    name: string;
    email: string;
    role: 'admin' | 'organizer' | 'judge' | 'participant';
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
    [key: string]: unknown;
};

export type Auth = {
    user: User;
};

/* @chisel-passkeys */
export type Passkey = {
    id: number;
    name: string;
    authenticator: string | null;
    created_at_diff: string;
    last_used_at_diff: string | null;
};
/* @end-chisel-passkeys */
```

- [ ] **Step 2: Create the Laravel paginator type**

```ts
export type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

export type LaravelPaginator<T> = {
    data: T[];
    links: PaginationLink[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number | null;
    to: number | null;
};
```

- [ ] **Step 3: Re-export the new type module**

In `resources/js/types/index.ts`, add the new module to the existing re-exports:

```ts
export type * from './auth';
export type * from './navigation';
export type * from './pagination';
export type * from './ui';
```

- [ ] **Step 4: Verify the project still type-checks**

Run: `npm run types:check`
Expected: No errors (existing code that reads `auth.user` is unaffected since `role` is additive).

- [ ] **Step 5: Commit**

```bash
git add resources/js/types/auth.ts resources/js/types/pagination.ts resources/js/types/index.ts
git commit -m "feat: add role to shared User type and a Laravel paginator type"
```

---

## Task 2: shadcn `Table` primitive

**Files:**
- Create: `resources/js/components/ui/table.tsx`

- [ ] **Step 1: Create the table primitive**

This is the standard shadcn/ui `Table` component (new-york style, matching this project's existing `components.json` config and the other files already in `resources/js/components/ui/`):

```tsx
import * as React from 'react';
import { cn } from '@/lib/utils';

function Table({ className, ...props }: React.ComponentProps<'table'>) {
    return (
        <div data-slot="table-container" className="relative w-full overflow-x-auto">
            <table data-slot="table" className={cn('w-full caption-bottom text-sm', className)} {...props} />
        </div>
    );
}

function TableHeader({ className, ...props }: React.ComponentProps<'thead'>) {
    return <thead data-slot="table-header" className={cn('[&_tr]:border-b', className)} {...props} />;
}

function TableBody({ className, ...props }: React.ComponentProps<'tbody'>) {
    return <tbody data-slot="table-body" className={cn('[&_tr:last-child]:border-0', className)} {...props} />;
}

function TableFooter({ className, ...props }: React.ComponentProps<'tfoot'>) {
    return (
        <tfoot
            data-slot="table-footer"
            className={cn('bg-muted/50 border-t font-medium [&>tr]:last:border-b-0', className)}
            {...props}
        />
    );
}

function TableRow({ className, ...props }: React.ComponentProps<'tr'>) {
    return (
        <tr
            data-slot="table-row"
            className={cn('hover:bg-muted/50 data-[state=selected]:bg-muted border-b transition-colors', className)}
            {...props}
        />
    );
}

function TableHead({ className, ...props }: React.ComponentProps<'th'>) {
    return (
        <th
            data-slot="table-head"
            className={cn(
                'text-foreground h-10 px-2 text-left align-middle font-medium whitespace-nowrap [&:has([role=checkbox])]:pr-0 [&>[role=checkbox]]:translate-y-[2px]',
                className,
            )}
            {...props}
        />
    );
}

function TableCell({ className, ...props }: React.ComponentProps<'td'>) {
    return (
        <td
            data-slot="table-cell"
            className={cn(
                'p-2 align-middle whitespace-nowrap [&:has([role=checkbox])]:pr-0 [&>[role=checkbox]]:translate-y-[2px]',
                className,
            )}
            {...props}
        />
    );
}

function TableCaption({ className, ...props }: React.ComponentProps<'caption'>) {
    return <caption data-slot="table-caption" className={cn('text-muted-foreground mt-4 text-sm', className)} {...props} />;
}

export { Table, TableHeader, TableBody, TableFooter, TableHead, TableRow, TableCell, TableCaption };
```

- [ ] **Step 2: Verify lint and types pass**

Run: `npm run lint:check && npm run types:check`
Expected: No errors.

- [ ] **Step 3: Commit**

```bash
git add resources/js/components/ui/table.tsx
git commit -m "feat: add shadcn Table primitive"
```

---

## Task 3: Admin dashboard route, controller, and page

**Files:**
- Create: `routes/admin.php`
- Modify: `routes/web.php`
- Create: `app/Http/Controllers/Admin/DashboardController.php`
- Create: `resources/js/pages/admin/index.tsx`
- Test: `tests/Feature/Admin/DashboardTest.php`

- [ ] **Step 1: Write the failing feature test**

```php
<?php

use App\Models\User;

it('allows an admin to view the admin dashboard', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->get('/admin');

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('admin/index'));
});

it('forbids a non-admin from viewing the admin dashboard', function () {
    $organizer = User::factory()->organizer()->create();

    $this->actingAs($organizer)->get('/admin')->assertForbidden();
});

it('redirects guests to login', function () {
    $this->get('/admin')->assertRedirect('/login');
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact tests/Feature/Admin/DashboardTest.php`
Expected: FAIL with a 404 (route `/admin` doesn't exist yet)

- [ ] **Step 3: Create the controller**

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('admin/index');
    }
}
```

- [ ] **Step 4: Create the route file**

```php
<?php

use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
});
```

- [ ] **Step 5: Require the route file from `routes/web.php`**

```php
<?php

use Illuminate\Support\Facades\Route;

Route::inertia('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
require __DIR__.'/admin.php';
```

- [ ] **Step 6: Generate the typed Wayfinder route helper**

```bash
php artisan wayfinder:generate --with-form --no-interaction
```

This creates `resources/js/routes/admin/index.ts` exporting a `dashboard()` function for the new `admin.dashboard` named route.

- [ ] **Step 7: Create the Inertia page**

```tsx
import { Head } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { dashboard } from '@/routes/admin';
import type { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Admin Dashboard', href: dashboard() }];

export default function AdminDashboard() {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Admin Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <div className="rounded-xl border border-sidebar-border/70 p-6 dark:border-sidebar-border">
                    <h1 className="text-lg font-semibold">Welcome, Admin</h1>
                    <p className="text-muted-foreground text-sm">
                        This is your dashboard. Resource management will appear here.
                    </p>
                </div>
            </div>
        </AppLayout>
    );
}
```

- [ ] **Step 8: Run test to verify it passes**

Run: `php artisan test --compact tests/Feature/Admin/DashboardTest.php`
Expected: PASS

- [ ] **Step 9: Commit**

```bash
vendor/bin/pint --dirty --format agent
git add routes/admin.php routes/web.php app/Http/Controllers/Admin/DashboardController.php resources/js/pages/admin/index.tsx resources/js/routes/admin tests/Feature/Admin/DashboardTest.php
git commit -m "feat: add admin dashboard landing route and page"
```

---

## Task 4: Organizer dashboard route, controller, and page

**Files:**
- Create: `routes/organizer.php`
- Modify: `routes/web.php`
- Create: `app/Http/Controllers/Organizer/DashboardController.php`
- Create: `resources/js/pages/organizer/index.tsx`
- Test: `tests/Feature/Organizer/DashboardTest.php`

- [ ] **Step 1: Write the failing feature test**

```php
<?php

use App\Models\User;

it('allows an organizer to view the organizer dashboard', function () {
    $organizer = User::factory()->organizer()->create();

    $response = $this->actingAs($organizer)->get('/organizer');

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('organizer/index'));
});

it('forbids a non-organizer from viewing the organizer dashboard', function () {
    $judge = User::factory()->judge()->create();

    $this->actingAs($judge)->get('/organizer')->assertForbidden();
});

it('redirects guests to login', function () {
    $this->get('/organizer')->assertRedirect('/login');
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact tests/Feature/Organizer/DashboardTest.php`
Expected: FAIL with a 404

- [ ] **Step 3: Create the controller**

```php
<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('organizer/index');
    }
}
```

- [ ] **Step 4: Create the route file**

```php
<?php

use App\Http\Controllers\Organizer\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:organizer'])->prefix('organizer')->name('organizer.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
});
```

- [ ] **Step 5: Require the route file from `routes/web.php`**

```php
require __DIR__.'/admin.php';
require __DIR__.'/organizer.php';
```

- [ ] **Step 6: Generate the typed Wayfinder route helper**

```bash
php artisan wayfinder:generate --with-form --no-interaction
```

- [ ] **Step 7: Create the Inertia page**

```tsx
import { Head } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { dashboard } from '@/routes/organizer';
import type { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Organizer Dashboard', href: dashboard() }];

export default function OrganizerDashboard() {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Organizer Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <div className="rounded-xl border border-sidebar-border/70 p-6 dark:border-sidebar-border">
                    <h1 className="text-lg font-semibold">Welcome, Organizer</h1>
                    <p className="text-muted-foreground text-sm">
                        This is your dashboard. Your competitions will appear here.
                    </p>
                </div>
            </div>
        </AppLayout>
    );
}
```

- [ ] **Step 8: Run test to verify it passes**

Run: `php artisan test --compact tests/Feature/Organizer/DashboardTest.php`
Expected: PASS

- [ ] **Step 9: Commit**

```bash
vendor/bin/pint --dirty --format agent
git add routes/organizer.php routes/web.php app/Http/Controllers/Organizer/DashboardController.php resources/js/pages/organizer/index.tsx resources/js/routes/organizer tests/Feature/Organizer/DashboardTest.php
git commit -m "feat: add organizer dashboard landing route and page"
```

---

## Task 5: Judge dashboard route, controller, and page

**Files:**
- Create: `routes/judge.php`
- Modify: `routes/web.php`
- Create: `app/Http/Controllers/Judge/DashboardController.php`
- Create: `resources/js/pages/judge/index.tsx`
- Test: `tests/Feature/Judge/DashboardTest.php`

- [ ] **Step 1: Write the failing feature test**

```php
<?php

use App\Models\User;

it('allows a judge to view the judge dashboard', function () {
    $judge = User::factory()->judge()->create();

    $response = $this->actingAs($judge)->get('/judge');

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('judge/index'));
});

it('forbids a non-judge from viewing the judge dashboard', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->get('/judge')->assertForbidden();
});

it('redirects guests to login', function () {
    $this->get('/judge')->assertRedirect('/login');
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact tests/Feature/Judge/DashboardTest.php`
Expected: FAIL with a 404

- [ ] **Step 3: Create the controller**

```php
<?php

namespace App\Http\Controllers\Judge;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('judge/index');
    }
}
```

- [ ] **Step 4: Create the route file**

```php
<?php

use App\Http\Controllers\Judge\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:judge'])->prefix('judge')->name('judge.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
});
```

- [ ] **Step 5: Require the route file from `routes/web.php`**

```php
require __DIR__.'/admin.php';
require __DIR__.'/organizer.php';
require __DIR__.'/judge.php';
```

- [ ] **Step 6: Generate the typed Wayfinder route helper**

```bash
php artisan wayfinder:generate --with-form --no-interaction
```

- [ ] **Step 7: Create the Inertia page**

```tsx
import { Head } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { dashboard } from '@/routes/judge';
import type { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Judge Dashboard', href: dashboard() }];

export default function JudgeDashboard() {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Judge Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <div className="rounded-xl border border-sidebar-border/70 p-6 dark:border-sidebar-border">
                    <h1 className="text-lg font-semibold">Welcome, Judge</h1>
                    <p className="text-muted-foreground text-sm">
                        This is your dashboard. Competitions assigned to you will appear here.
                    </p>
                </div>
            </div>
        </AppLayout>
    );
}
```

- [ ] **Step 8: Run test to verify it passes**

Run: `php artisan test --compact tests/Feature/Judge/DashboardTest.php`
Expected: PASS

- [ ] **Step 9: Commit**

```bash
vendor/bin/pint --dirty --format agent
git add routes/judge.php routes/web.php app/Http/Controllers/Judge/DashboardController.php resources/js/pages/judge/index.tsx resources/js/routes/judge tests/Feature/Judge/DashboardTest.php
git commit -m "feat: add judge dashboard landing route and page"
```

---

## Task 6: Role-aware sidebar navigation

**Files:**
- Modify: `resources/js/components/app-sidebar.tsx`

- [ ] **Step 1: Replace the hardcoded nav items with role-aware selection**

Replace the full contents of `resources/js/components/app-sidebar.tsx`:

```tsx
import { Link, usePage } from '@inertiajs/react';
import { BookOpen, FolderGit2, LayoutGrid } from 'lucide-react';
import AppLogo from '@/components/app-logo';
import { NavFooter } from '@/components/nav-footer';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { dashboard as adminDashboard } from '@/routes/admin';
import { dashboard as organizerDashboard } from '@/routes/organizer';
import { dashboard as judgeDashboard } from '@/routes/judge';
import type { NavItem, User } from '@/types';

const adminNavItems: NavItem[] = [{ title: 'Dashboard', href: adminDashboard(), icon: LayoutGrid }];

const organizerNavItems: NavItem[] = [{ title: 'Dashboard', href: organizerDashboard(), icon: LayoutGrid }];

const judgeNavItems: NavItem[] = [{ title: 'Dashboard', href: judgeDashboard(), icon: LayoutGrid }];

const footerNavItems: NavItem[] = [
    {
        title: 'Repository',
        href: 'https://github.com/laravel/react-starter-kit',
        icon: FolderGit2,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#react',
        icon: BookOpen,
    },
];

function navItemsForRole(role: User['role'] | undefined): NavItem[] {
    switch (role) {
        case 'admin':
            return adminNavItems;
        case 'organizer':
            return organizerNavItems;
        case 'judge':
            return judgeNavItems;
        default:
            return [];
    }
}

export function AppSidebar() {
    const { auth } = usePage().props;
    const navItems = navItemsForRole(auth.user?.role);

    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href={dashboard()} prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <NavMain items={navItems} />
            </SidebarContent>

            <SidebarFooter>
                <NavFooter items={footerNavItems} className="mt-auto" />
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
```

- [ ] **Step 2: Verify lint and types pass**

Run: `npm run lint:check && npm run types:check`
Expected: No errors.

- [ ] **Step 3: Commit**

```bash
vendor/bin/pint --dirty --format agent
git add resources/js/components/app-sidebar.tsx
git commit -m "feat: make sidebar navigation role-aware"
```

---

## Task 7: Shared `DataTable` component

**Files:**
- Create: `resources/js/components/data-table.tsx`

- [ ] **Step 1: Create the component**

```tsx
import { Link } from '@inertiajs/react';
import type { ReactNode } from 'react';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { cn } from '@/lib/utils';
import type { LaravelPaginator } from '@/types';

export type DataTableColumn<T> = {
    header: string;
    cell: (row: T) => ReactNode;
    className?: string;
};

export function DataTable<T extends { id: number | string }>({
    columns,
    paginator,
}: {
    columns: DataTableColumn<T>[];
    paginator: LaravelPaginator<T>;
}) {
    return (
        <div className="flex flex-col gap-4">
            <Table>
                <TableHeader>
                    <TableRow>
                        {columns.map((column) => (
                            <TableHead key={column.header} className={column.className}>
                                {column.header}
                            </TableHead>
                        ))}
                    </TableRow>
                </TableHeader>
                <TableBody>
                    {paginator.data.length === 0 ? (
                        <TableRow>
                            <TableCell colSpan={columns.length} className="text-muted-foreground text-center">
                                No results.
                            </TableCell>
                        </TableRow>
                    ) : (
                        paginator.data.map((row) => (
                            <TableRow key={row.id}>
                                {columns.map((column) => (
                                    <TableCell key={column.header} className={column.className}>
                                        {column.cell(row)}
                                    </TableCell>
                                ))}
                            </TableRow>
                        ))
                    )}
                </TableBody>
            </Table>

            {paginator.links.length > 3 && (
                <nav className="flex flex-wrap items-center gap-1">
                    {paginator.links.map((link, index) => (
                        <Link
                            key={index}
                            href={link.url ?? '#'}
                            preserveScroll
                            className={cn(
                                'rounded-md px-3 py-1.5 text-sm',
                                link.active ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:bg-muted',
                                !link.url && 'pointer-events-none opacity-50',
                            )}
                            dangerouslySetInnerHTML={{ __html: link.label }}
                        />
                    ))}
                </nav>
            )}
        </div>
    );
}
```

- [ ] **Step 2: Verify lint and types pass**

Run: `npm run lint:check && npm run types:check`
Expected: No errors.

- [ ] **Step 3: Commit**

```bash
git add resources/js/components/data-table.tsx
git commit -m "feat: add shared DataTable component"
```

---

## Task 8: Full suite check

**Files:** none (verification only)

- [ ] **Step 1: Run the full backend test suite**

Run: `php artisan test --compact`
Expected: All tests PASS, including the new Admin/Organizer/Judge dashboard tests plus every test from sub-project 1.

- [ ] **Step 2: Run Pint across the whole app**

Run: `vendor/bin/pint --format agent`
Expected: No remaining style violations.

- [ ] **Step 3: Run the frontend checks**

Run: `npm run types:check && npm run lint:check`
Expected: No errors.

- [ ] **Step 4: Run Larastan**

Run: `vendor/bin/phpstan analyse --no-interaction --memory-limit=1G`
Expected: No new errors introduced by this plan's code (pre-existing baseline errors are out of scope).

- [ ] **Step 5: Commit any final formatting fixes**

```bash
git add -A
git commit -m "chore: final formatting pass for shared dashboard shell"
```

(Skip this commit if there is nothing to stage.)
