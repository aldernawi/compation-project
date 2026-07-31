class WinnerModel {
  final int id;
  final Participant? participant;
  final Prize? prize;

  WinnerModel({required this.id, this.participant, this.prize});

  factory WinnerModel.fromJson(Map<String, dynamic> json) {
    return WinnerModel(
      id: json['id'] as int,
      participant: json['participant'] != null
          ? Participant.fromJson(json['participant'] as Map<String, dynamic>)
          : null,
      prize: json['prize'] != null
          ? Prize.fromJson(json['prize'] as Map<String, dynamic>)
          : null,
    );
  }
}

class Participant {
  final int id;
  final String name;

  Participant({required this.id, required this.name});

  factory Participant.fromJson(Map<String, dynamic> json) {
    return Participant(
      id: json['id'] as int,
      name: json['name'] as String,
    );
  }
}

class Prize {
  final int id;
  final String title;
  final int rank;

  Prize({required this.id, required this.title, required this.rank});

  factory Prize.fromJson(Map<String, dynamic> json) {
    return Prize(
      id: json['id'] as int,
      title: json['title'] as String,
      rank: json['rank'] as int? ?? 0,
    );
  }
}
