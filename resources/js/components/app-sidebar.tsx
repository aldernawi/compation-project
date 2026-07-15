import { Link, usePage } from '@inertiajs/react';
import { BarChart3, BookOpen, FileText, FolderGit2, LayoutGrid, Tags, Trophy, Users } from 'lucide-react';
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
import { index as adminCompetitionTypesIndex } from '@/routes/admin/competition-types';
import { index as adminCompetitionsIndex } from '@/routes/admin/competitions';
import { index as adminReportsIndex } from '@/routes/admin/reports';
import { index as adminSubmissionsIndex } from '@/routes/admin/submissions';
import { index as adminUsersIndex } from '@/routes/admin/users';
import { dashboard as judgeDashboard } from '@/routes/judge';
import { dashboard as organizerDashboard } from '@/routes/organizer';
import type { NavItem, User } from '@/types';

const adminNavItems: NavItem[] = [
    { title: 'Dashboard', href: adminDashboard(), icon: LayoutGrid },
    { title: 'Users', href: adminUsersIndex(), icon: Users },
    { title: 'Competition Types', href: adminCompetitionTypesIndex(), icon: Tags },
    { title: 'Competitions', href: adminCompetitionsIndex(), icon: Trophy },
    { title: 'Submissions', href: adminSubmissionsIndex(), icon: FileText },
    { title: 'Reports', href: adminReportsIndex(), icon: BarChart3 },
];

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
