class NotificationModel {
  final String id;
  final String type;
  final Map<String, dynamic> data;
  final DateTime? createdAt;
  final DateTime? readAt;
  bool get isRead => readAt != null;

  NotificationModel({
    required this.id,
    required this.type,
    required this.data,
    this.createdAt,
    this.readAt,
  });

  factory NotificationModel.fromJson(Map<String, dynamic> json) {
    return NotificationModel(
      id: json['id'] as String,
      type: json['type'] as String? ?? '',
      data: json['data'] as Map<String, dynamic>? ?? {},
      createdAt: json['created_at'] != null ? DateTime.parse(json['created_at']) : null,
      readAt: json['read_at'] != null ? DateTime.parse(json['read_at']) : null,
    );
  }

  String get title {
    if (data.containsKey('status')) {
      switch (data['status']) {
        case 'accepted':
          return 'تم قبول مشاركتك';
        case 'rejected':
          return 'تم رفض مشاركتك';
        case 'under_review':
          return 'مشاركتك قيد المراجعة';
        case 'under_evaluation':
          return 'بدء التقييم';
        case 'evaluated':
          return 'تم التقييم';
        default:
          return 'تحديث المشاركة';
      }
    }
    if (data.containsKey('message')) {
      return data['message'] as String? ?? 'إشعار';
    }
    return 'إشعار جديد';
  }

  String get body {
    if (data.containsKey('message')) {
      return data['message'] as String? ?? '';
    }
    if (data.containsKey('status')) {
      switch (data['status']) {
        case 'accepted':
          return 'تم قبول مشاركتك في المسابقة';
        case 'rejected':
          return 'تم رفض مشاركتك، تحقق من التفاصيل';
        case 'under_review':
          return 'مشاركتك قيد المراجعة من قبل المنظم';
        case 'under_evaluation':
          return 'بدأ المحكمون في تقييم مشاركتك';
        case 'evaluated':
          return 'تم تقييم مشاركتك، تحقق من النتيجة';
        default:
          return 'تم تحديث حالة مشاركتك';
      }
    }
    return '';
  }
}
