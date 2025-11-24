class UserModel {
  final int id;
  final String username;
  final String displayName;
  final String email;
  final String token;
  final List<String> roles;
  final String? level;
  final String? avatarUrl;

  UserModel({
    required this.id,
    required this.username,
    required this.displayName,
    required this.email,
    required this.token,
    required this.roles,
    this.level,
    this.avatarUrl,
  });

  factory UserModel.fromJson(Map<String, dynamic> json) {
    return UserModel(
      id: json['user_id'] ?? json['id'],
      username: json['username'],
      displayName: json['display_name'],
      email: json['email'],
      token: json['token'] ?? '',
      roles: List<String>.from(json['roles'] ?? []),
      level: json['level']?.toString(),
      avatarUrl: json['avatar_url'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'username': username,
      'display_name': displayName,
      'email': email,
      'token': token,
      'roles': roles,
      'level': level,
      'avatar_url': avatarUrl,
    };
  }
}
