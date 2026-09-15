import { usePage } from '@inertiajs/react';
import type { ReactElement } from 'react';
import AppLayout from '@/layouts/app-layout';
import type { PageProps } from '@/types';
import { StudentDashboard } from './dashboard/student-dashboard';
import { TeacherDashboard } from './dashboard/teacher-dashboard';
import type {
    StudentDashboardData,
    TeacherDashboardData,
} from './dashboard/types';

interface HomeProps extends Record<string, unknown> {
    teacherDashboard: TeacherDashboardData | null;
    studentDashboard: StudentDashboardData | null;
}

export default function Home(): ReactElement {
    const { auth, teacherDashboard, studentDashboard } =
        usePage<PageProps<HomeProps>>().props;

    const description = teacherDashboard
        ? 'Panorama dos exames de todo o laboratório.'
        : studentDashboard
          ? 'Acompanhamento dos exames que você realizou.'
          : 'Resumo da sua atividade na Clínica Unicesumar.';

    return (
        <AppLayout title="Dashboard">
            <div className="mb-2">
                <h2 className="text-2xl font-bold">
                    Bem-vindo{auth.user ? `, ${auth.user.name}` : ''}!
                </h2>
                <p className="text-muted-foreground">{description}</p>
            </div>

            {teacherDashboard && <TeacherDashboard data={teacherDashboard} />}
            {studentDashboard && <StudentDashboard data={studentDashboard} />}
        </AppLayout>
    );
}
