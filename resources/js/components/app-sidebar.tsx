import { Link, usePage } from '@inertiajs/react';
import { BarChart3, FileText, LayoutGrid, Tags, Trophy, Users } from 'lucide-react';
import AppLogo from '@/components/app-logo';
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
import { index as judgeCompetitionsIndex } from '@/routes/judge/competitions';
import { dashboard as organizerDashboard } from '@/routes/organizer';
import { index as organizerCompetitionsIndex } from '@/routes/organizer/competitions';
import type { NavItem, User } from '@/types';

const adminNavItems: NavItem[] = [
    { title: 'الرئيسية', href: adminDashboard(), icon: LayoutGrid },
    { title: 'المستخدمون', href: adminUsersIndex(), icon: Users },
    { title: 'أنواع المسابقات', href: adminCompetitionTypesIndex(), icon: Tags },
    { title: 'المسابقات', href: adminCompetitionsIndex(), icon: Trophy },
    { title: 'المشاركات', href: adminSubmissionsIndex(), icon: FileText },
    { title: 'التقارير', href: adminReportsIndex(), icon: BarChart3 },
];

const organizerNavItems: NavItem[] = [
    { title: 'الرئيسية', href: organizerDashboard(), icon: LayoutGrid },
    { title: 'مسابقاتي', href: organizerCompetitionsIndex(), icon: Trophy },
];

const judgeNavItems: NavItem[] = [
    { title: 'الرئيسية', href: judgeDashboard(), icon: LayoutGrid },
    { title: 'المسابقات المعينة', href: judgeCompetitionsIndex(), icon: Trophy },
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
        <Sidebar collapsible="icon" variant="inset" side="right">
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
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
