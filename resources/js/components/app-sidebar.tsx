import { Link, usePage } from '@inertiajs/react';
import {
    ClipboardList,
    FlaskConical,
    History,
    LayoutDashboard,
    Microscope,
    Settings2,
    TestTubes,
    UserCog,
    type LucideIcon,
} from 'lucide-react';
import { NavUser } from '@/components/nav-user';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarGroup,
    SidebarGroupLabel,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import type { PageProps, UserRole } from '@/types';

interface NavItem {
    title: string;
    routeName: string;
    icon: LucideIcon;
    /** Padrão Ziggy para marcar o item ativo (ex.: 'samples.*'). */
    activePattern: string;
    roles: UserRole[];
}

const NAV_ITEMS: readonly NavItem[] = [
    {
        title: 'Dashboard',
        routeName: 'home',
        icon: LayoutDashboard,
        activePattern: 'home',
        roles: ['teacher', 'student'],
    },
    {
        title: 'Amostras',
        routeName: 'samples.index',
        icon: FlaskConical,
        activePattern: 'samples.*',
        roles: ['teacher', 'student'],
    },
    {
        title: 'Anamneses',
        routeName: 'patient-histories.index',
        icon: ClipboardList,
        activePattern: 'patient-histories.*',
        roles: ['teacher', 'student'],
    },
    {
        title: 'Exames',
        routeName: 'exam.index',
        icon: Microscope,
        activePattern: 'exam.*',
        roles: ['teacher', 'student'],
    },
    {
        title: 'Máquinas',
        routeName: 'machines.index',
        icon: Settings2,
        activePattern: 'machines.*',
        roles: ['teacher'],
    },
    {
        title: 'Usuários',
        routeName: 'user-management.index',
        icon: UserCog,
        activePattern: 'user-management.*',
        roles: ['teacher'],
    },
    {
        title: 'Logs',
        routeName: 'activity-logs.index',
        icon: History,
        activePattern: 'activity-logs.*',
        roles: ['teacher'],
    },
    {
        title: 'Meus Exames',
        routeName: 'patient-exams.index',
        icon: TestTubes,
        activePattern: 'patient-exams.*',
        roles: ['patient'],
    },
] as const;

export function AppSidebar() {
    const { auth } = usePage<PageProps>().props;
    const role = auth.user?.role;

    const items = role
        ? NAV_ITEMS.filter((item) => item.roles.includes(role))
        : [];

    return (
        <Sidebar collapsible="icon">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href="/home">
                                <div className="flex aspect-square size-8 items-center justify-center rounded-lg bg-primary text-primary-foreground">
                                    <img
                                        src="/imgs/unicesumar-logo.png"
                                        alt="Unicesumar"
                                        className="size-6 object-contain"
                                    />
                                </div>
                                <div className="grid flex-1 text-left text-sm leading-tight">
                                    <span className="truncate font-semibold">
                                        Clínica Unicesumar
                                    </span>
                                </div>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <SidebarGroup>
                    <SidebarGroupLabel>Navegação</SidebarGroupLabel>
                    <SidebarMenu>
                        {items.map((item) => (
                            <SidebarMenuItem key={item.routeName}>
                                <SidebarMenuButton
                                    asChild
                                    isActive={route().current(
                                        item.activePattern,
                                    )}
                                    tooltip={item.title}
                                >
                                    <Link href={route(item.routeName)}>
                                        <item.icon />
                                        <span>{item.title}</span>
                                    </Link>
                                </SidebarMenuButton>
                            </SidebarMenuItem>
                        ))}
                    </SidebarMenu>
                </SidebarGroup>
            </SidebarContent>

            <SidebarFooter>
                {auth.user && <NavUser user={auth.user} />}
            </SidebarFooter>
        </Sidebar>
    );
}
