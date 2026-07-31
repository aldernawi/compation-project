class SubmissionModel {
  final int id;
  final int competitionId;
  final CompetitionBrief? competition;
  final String status;
  final String? textContent;
  final String? linkUrl;
  final String? rejectionReason;
  final String? mediaUrl;
  final double? averageScore;
  final bool isWinner;
  final int? rank;
  final DateTime? createdAt;

  SubmissionModel({
    required this.id,
    required this.competitionId,
    this.competition,
    required this.status,
    this.textContent,
    this.linkUrl,
    this.rejectionReason,
    this.mediaUrl,
    this.averageScore,
    this.isWinner = false,
    this.rank,
    this.createdAt,
  });

  factory SubmissionModel.fromJson(Map<String, dynamic> json) {
    return SubmissionModel(
      id: json['id'] as int,
      competitionId: json['competition_id'] as int,
      competition: json['competition'] != null
          ? CompetitionBrief.fromJson(json['competition'] as Map<String, dynamic>)
          : null,
      status: json['status'] as String,
      textContent: json['text_content'] as String?,
      linkUrl: json['link_url'] as String?,
      rejectionReason: json['rejection_reason'] as String?,
      mediaUrl: json['media_url'] as String?,
      averageScore: (json['average_score'] as num?)?.toDouble(),
      isWinner: json['is_winner'] as bool? ?? false,
      rank: json['rank'] as int?,
      createdAt: json['created_at'] != null ? DateTime.parse(json['created_at']) : null,
    );
  }
}

class CompetitionBrief {
  final int id;
  final String title;
  final String status;
  final DateTime? endsAt;

  CompetitionBrief({
    required this.id,
    required this.title,
    required this.status,
    this.endsAt,
  });

  factory CompetitionBrief.fromJson(Map<String, dynamic> json) {
    return CompetitionBrief(
      id: json['id'] as int,
      title: json['title'] as String,
      status: json['status'] as String,
      endsAt: json['ends_at'] != null ? DateTime.parse(json['ends_at']) : null,
    );
  }
}
