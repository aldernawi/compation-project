class CompetitionModel {
  final int id;
  final String title;
  final String? description;
  final String? terms;
  final String status;
  final DateTime? startsAt;
  final DateTime? endsAt;
  final bool requiresApproval;
  final String? evaluationMethod;
  final Organizer? organizer;
  final CompetitionType? competitionType;
  final List<PrizeModel> prizes;

  CompetitionModel({
    required this.id,
    required this.title,
    this.description,
    this.terms,
    required this.status,
    this.startsAt,
    this.endsAt,
    this.requiresApproval = false,
    this.evaluationMethod,
    this.organizer,
    this.competitionType,
    this.prizes = const [],
  });

  factory CompetitionModel.fromJson(Map<String, dynamic> json) {
    return CompetitionModel(
      id: json['id'] as int,
      title: json['title'] as String,
      description: json['description'] as String?,
      terms: json['terms'] as String?,
      status: json['status'] as String,
      startsAt: json['starts_at'] != null ? DateTime.parse(json['starts_at']) : null,
      endsAt: json['ends_at'] != null ? DateTime.parse(json['ends_at']) : null,
      requiresApproval: json['requires_approval'] as bool? ?? false,
      evaluationMethod: json['evaluation_method'] as String?,
      organizer: json['organizer'] != null
          ? Organizer.fromJson(json['organizer'] as Map<String, dynamic>)
          : null,
      competitionType: json['competition_type'] != null
          ? CompetitionType.fromJson(json['competition_type'] as Map<String, dynamic>)
          : null,
      prizes: (json['prizes'] as List<dynamic>?)
              ?.map((e) => PrizeModel.fromJson(e as Map<String, dynamic>))
              .toList() ??
          [],
    );
  }
}

class Organizer {
  final int id;
  final String name;

  Organizer({required this.id, required this.name});

  factory Organizer.fromJson(Map<String, dynamic> json) {
    return Organizer(
      id: json['id'] as int,
      name: json['name'] as String,
    );
  }
}

class CompetitionType {
  final int id;
  final String name;
  final String submissionKind;

  CompetitionType({
    required this.id,
    required this.name,
    required this.submissionKind,
  });

  factory CompetitionType.fromJson(Map<String, dynamic> json) {
    return CompetitionType(
      id: json['id'] as int,
      name: json['name'] as String,
      submissionKind: json['submission_kind'] as String,
    );
  }
}

class PrizeModel {
  final int id;
  final String title;
  final String? description;
  final int winnersCount;
  final int rank;

  PrizeModel({
    required this.id,
    required this.title,
    this.description,
    required this.winnersCount,
    required this.rank,
  });

  factory PrizeModel.fromJson(Map<String, dynamic> json) {
    return PrizeModel(
      id: json['id'] as int,
      title: json['title'] as String,
      description: json['description'] as String?,
      winnersCount: json['winners_count'] as int? ?? 1,
      rank: json['rank'] as int? ?? 0,
    );
  }
}
