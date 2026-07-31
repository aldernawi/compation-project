export const competitionStatusLabels: Record<string, string> = {
    upcoming: 'قادمة',
    open: 'مفتوحة',
    closed: 'مغلقة',
    under_evaluation: 'قيد التقييم',
    finished: 'منتهية',
};

export const submissionStatusLabels: Record<string, string> = {
    submitted: 'تم الإرسال',
    under_review: 'قيد المراجعة',
    accepted: 'مقبولة',
    rejected: 'مرفوضة',
    under_evaluation: 'قيد التقييم',
    evaluated: 'تم التقييم',
};

export const submissionKindLabels: Record<string, string> = {
    image: 'صورة',
    pdf: 'PDF',
    video: 'فيديو',
    text: 'نص',
    link: 'رابط',
    none: 'تسجيل فقط',
};

export const userRoleLabels: Record<string, string> = {
    admin: 'مدير',
    organizer: 'منظم',
    judge: 'حكم',
    participant: 'مشارك',
};

export function translateCompetitionStatus(status: string): string {
    return competitionStatusLabels[status] ?? status;
}

export function translateSubmissionStatus(status: string): string {
    return submissionStatusLabels[status] ?? status;
}

export function translateSubmissionKind(kind: string): string {
    return submissionKindLabels[kind] ?? kind;
}

export function translateUserRole(role: string): string {
    return userRoleLabels[role] ?? role;
}
