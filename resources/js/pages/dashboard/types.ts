import type {
    CalibrationStatus,
    ExamStatus,
    MachineStatus,
    SampleStatus,
} from '@/types';

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

export interface ApprovalQueueItem {
    id: number;
    student: string;
    exam_type: string;
    patient: string;
    submitted_at: string;
    waiting_hours: number;
    is_stale: boolean;
}

export interface RankedStudent {
    name: string;
    total: number;
}

export interface ExamTypeVolume {
    label: string;
    total: number;
}

export interface SampleStatusCount {
    status: SampleStatus;
    label: string;
    count: number;
}

export interface OverdueSample {
    id: number;
    code: string;
    patient: string;
    days: number;
}

export interface SamplesOverview {
    distribution: SampleStatusCount[];
    overdueInFreezer: OverdueSample[];
}

export interface MachineOverview {
    id: number;
    name: string;
    status: MachineStatus;
    last_calibration_at: string | null;
    last_calibration_status: CalibrationStatus | null;
    is_calibration_due: boolean;
}

export interface TeacherDashboardData {
    stats: DashboardStat[];
    approvalQueue: ApprovalQueueItem[];
    staleApprovalsCount: number;
    statusDistribution: StatusCount[];
    monthlyVolume: MonthlyCount[];
    rejectionRateByExamType: ExamTypeRejectionRate[];
    rejectionRanking: RankedStudent[];
    topExamTypes: ExamTypeVolume[];
    activeStudents: RankedStudent[];
    inactiveStudents: string[];
    samples: SamplesOverview;
    machines: MachineOverview[];
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
