class UserModel {
  final int id;
  final String name;
  final String email;
  final String? phoneNumber;
  final int? age;
  final String role;

  UserModel({
    required this.id,
    required this.name,
    required this.email,
    this.phoneNumber,
    this.age,
    required this.role,
  });

  factory UserModel.fromJson(Map<String, dynamic> json) {
    return UserModel(
      id: json['id'] as int,
      name: json['name'] as String,
      email: json['email'] as String,
      phoneNumber: json['phone_number'] as String?,
      age: json['age'] as int?,
      role: json['role'] as String,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'email': email,
      'phone_number': phoneNumber,
      'age': age,
      'role': role,
    };
  }
}
