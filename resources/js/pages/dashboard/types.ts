import type { ExamStatus } from '@/types';

export type StatTone = 'primary' | 'success' | 'warning' | 'danger';

export interface DashboardStat {
    label: string;
    value: number;
    tone: StatTone;
    hint: string | null;
    suffix: string | null;
}

export interface StatusCount {
    status: ExamStatus;
    label: string;
    count: number;
}

export interface MonthlyCount {
    label: string;
    count: number;
}

export interface ExamTypeRejectionRate {
    label: string;
    rate: number;
    rejected: number;
    total: number;
}

export interface StaleApproval {
    id: number;
    student: string;
    exam_type: string;
    patient: string;
    waiting_hours: number;
}

export interface RankedStudent {
    name: string;
    total: number;
}

export interface TeacherDashboardData {
    stats: DashboardStat[];
    statusDistribution: StatusCount[];
    monthlyVolume: MonthlyCount[];
    rejectionRateByExamType: ExamTypeRejectionRate[];
    staleApprovals: StaleApproval[];
    rejectionRanking: RankedStudent[];
    inactiveStudents: string[];
}

export interface ActionRequiredExam {
    id: number;
    exam_type: string;
    patient: string;
    status: ExamStatus;
    date: string | null;
}

export interface RecentRejection {
    exam_id: number;
    exam_type: string;
    justification: string;
    rejected_by: string;
    rejected_at: string | null;
}

export interface StudentDashboardData {
    stats: DashboardStat[];
    statusDistribution: StatusCount[];
    monthlyVolume: MonthlyCount[];
    actionRequired: ActionRequiredExam[];
    recentRejections: RecentRejection[];
}
