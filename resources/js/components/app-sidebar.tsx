import { Link, usePage } from '@inertiajs/react';
import {
    Boxes,
    CalendarClock,
    ClipboardList,
    FlaskConical,
    History,
    LayoutDashboard,
    ListChecks,
    Megaphone,
    Microscope,
    Settings2,
    TestTubes,
    UserCog,
    Users,
    type LucideIcon,
} from 'lucide-react';
import { NavUser } from '@/components/nav-user';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import type { PageProps, UserRole } from '@/types';

interface NavItem {
    title: string;
    routeName: string;
    icon: LucideIcon;
    activePattern: string;
    roles: UserRole[];
}

interface NavGroup {
    label: string;
    items: readonly NavItem[];
}

const NAV_GROUPS: readonly NavGroup[] = [
    {
        label: 'Principal',
        items: [
            {
                title: 'Dashboard',
                routeName: 'home',
                icon: LayoutDashboard,
                activePattern: 'home',
                roles: ['teacher', 'student'],
            },
            {
                title: 'Meus Exames',
                routeName: 'patient-exams.index',
                icon: TestTubes,
                activePattern: 'patient-exams.*',
                roles: ['patient'],
            },
        ],
    },
    {
        label: 'Atendimento',
        items: [
            {
                title: 'Pacientes',
                routeName: 'patients.index',
                icon: Users,
                activePattern: 'patients.*',
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
                title: 'Amostras',
                routeName: 'samples.index',
                icon: FlaskConical,
                activePattern: 'samples.*',
                roles: ['teacher', 'student'],
            },
            {
                title: 'Exames',
                routeName: 'exam.index',
                icon: Microscope,
                activePattern: 'exam.*',
                roles: ['teacher', 'student'],
            },
        ],
    },
    {
        label: 'Cadastros',
        items: [
            {
                title: 'Tipos de Amostra',
                routeName: 'sample-type.index',
                icon: Boxes,
                activePattern: 'sample-type.*',
                roles: ['teacher'],
            },
            {
                title: 'Tipos de Exame',
                routeName: 'exam-type.index',
                icon: ListChecks,
                activePattern: 'exam-type.*',
                roles: ['teacher'],
            },
            {
                title: 'Máquinas',
                routeName: 'machines.index',
                icon: Settings2,
                activePattern: 'machines.*',
                roles: ['teacher'],
            },
            {
                title: 'Calibrações',
                routeName: 'calibrations.index',
                icon: CalendarClock,
                activePattern: 'calibrations.*',
                roles: ['teacher'],
            },
        ],
    },
    {
        label: 'Administração',
        items: [
            {
                title: 'Usuários',
                routeName: 'user-management.index',
                icon: UserCog,
                activePattern: 'user-management.*',
                roles: ['teacher'],
            },
            {
                title: 'Campanhas de Saúde',
                routeName: 'health-campaigns.index',
                icon: Megaphone,
                activePattern: 'health-campaigns.*',
                roles: ['teacher'],
            },
            {
                title: 'Logs',
                routeName: 'activity-logs.index',
                icon: History,
                activePattern: 'activity-logs.*',
                roles: ['teacher'],
            },
        ],
    },
] as const;

export function AppSidebar() {
    const { auth } = usePage<PageProps>().props;
    const role = auth.user?.role;

    const groups = role
        ? NAV_GROUPS.map((group) => ({
              ...group,
              items: group.items.filter((item) => item.roles.includes(role)),
          })).filter((group) => group.items.length > 0)
        : [];

    return (
        <Sidebar collapsible="icon">
            <SidebarContent>
                {groups.map((group) => (
                    <SidebarGroup key={group.label}>
                        <SidebarGroupLabel>{group.label}</SidebarGroupLabel>
                        <SidebarMenu>
                            {group.items.map((item) => (
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
                ))}
            </SidebarContent>

            <SidebarFooter>
                {auth.user && <NavUser user={auth.user} />}
            </SidebarFooter>
        </Sidebar>
    );
}
