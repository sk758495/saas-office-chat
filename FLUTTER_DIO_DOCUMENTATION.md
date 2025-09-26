# Flutter DIO API Documentation

## Setup DIO Client

```dart
import 'package:dio/dio.dart';

class ApiClient {
  static const String baseUrl = 'http://127.0.0.1:8000/api';
  late Dio _dio;
  String? _token;

  ApiClient() {
    _dio = Dio(BaseOptions(
      baseUrl: baseUrl,
      connectTimeout: Duration(seconds: 30),
      receiveTimeout: Duration(seconds: 30),
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
    ));

    _dio.interceptors.add(InterceptorsWrapper(
      onRequest: (options, handler) {
        if (_token != null) {
          options.headers['Authorization'] = 'Bearer $_token';
        }
        handler.next(options);
      },
    ));
  }

  void setToken(String token) {
    _token = token;
  }
}
```

## Authentication APIs

### 1. Register
```dart
Future<Map<String, dynamic>> register({
  required String name,
  required String email,
  required String password,
  required String passwordConfirmation,
  required int departmentId,
  required int designationId,
}) async {
  try {
    final response = await _dio.post('/register', data: {
      'name': name,
      'email': email,
      'password': password,
      'password_confirmation': passwordConfirmation,
      'department_id': departmentId,
      'designation_id': designationId,
    });
    return response.data;
  } catch (e) {
    throw _handleError(e);
  }
}

// Response Format:
{
  "success": true,
  "message": "Registration successful. Please verify your email.",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "department_id": 1,
    "designation_id": 1,
    "email_verified_at": null
  },
  "token": "1|abc123..."
}
```

### 2. Login
```dart
Future<Map<String, dynamic>> login({
  required String email,
  required String password,
}) async {
  try {
    final response = await _dio.post('/login', data: {
      'email': email,
      'password': password,
    });
    return response.data;
  } catch (e) {
    throw _handleError(e);
  }
}

// Response Format:
{
  "success": true,
  "message": "Login successful",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "department_id": 1,
    "designation_id": 1,
    "email_verified_at": "2024-01-01T00:00:00.000000Z"
  },
  "token": "1|abc123..."
}
```

## Chat APIs

### 1. Get All Chats
```dart
Future<Map<String, dynamic>> getChats() async {
  try {
    final response = await _dio.get('/chat');
    return response.data;
  } catch (e) {
    throw _handleError(e);
  }
}

// Response Format:
{
  "success": true,
  "chats": [
    {
      "id": 1,
      "user_id": 2,
      "other_user": {
        "id": 2,
        "name": "Jane Doe",
        "email": "jane@example.com",
        "profile_picture": null
      },
      "last_message": {
        "id": 10,
        "message": "Hello there!",
        "created_at": "2024-01-01T10:00:00.000000Z"
      },
      "unread_count": 3
    }
  ]
}
```

### 2. Send Message
```dart
Future<Map<String, dynamic>> sendMessage({
  required int receiverId,
  String? message,
  String? filePath,
  String? fileName,
}) async {
  try {
    FormData formData = FormData.fromMap({
      'receiver_id': receiverId,
      if (message != null) 'message': message,
      if (filePath != null) 'file': await MultipartFile.fromFile(filePath, filename: fileName),
    });

    final response = await _dio.post('/chat/send', data: formData);
    return response.data;
  } catch (e) {
    throw _handleError(e);
  }
}

// Response Format:
{
  "success": true,
  "message": "Message sent successfully",
  "data": {
    "id": 15,
    "chat_id": 1,
    "sender_id": 1,
    "receiver_id": 2,
    "message": "Hello there!",
    "file_path": null,
    "file_name": null,
    "created_at": "2024-01-01T10:00:00.000000Z",
    "sender": {
      "id": 1,
      "name": "John Doe"
    }
  }
}
```

## WebSocket Integration

### 1. Connect to WebSocket
```dart
Future<Map<String, dynamic>> getWebSocketConnection() async {
  try {
    final response = await _dio.get('/ws/connect');
    return response.data;
  } catch (e) {
    throw _handleError(e);
  }
}

// Response Format:
{
  "success": true,
  "websocket_url": "ws://127.0.0.1:6001/ws",
  "status": "ready_to_connect",
  "user_id": 1
}
```

### 2. WebSocket Client Implementation
```dart
import 'package:web_socket_channel/web_socket_channel.dart';

class WebSocketService {
  WebSocketChannel? _channel;
  String? _websocketUrl;
  int? _userId;

  Future<void> connect(String websocketUrl, int userId) async {
    _websocketUrl = websocketUrl;
    _userId = userId;
    
    try {
      _channel = WebSocketChannel.connect(Uri.parse(websocketUrl));
      
      // Send authentication
      _channel!.sink.add(jsonEncode({
        'type': 'auth',
        'user_id': userId,
      }));
      
      // Listen for messages
      _channel!.stream.listen(
        (message) {
          final data = jsonDecode(message);
          _handleMessage(data);
        },
        onError: (error) {
          print('WebSocket error: $error');
        },
        onDone: () {
          print('WebSocket connection closed');
        },
      );
    } catch (e) {
      print('Failed to connect to WebSocket: $e');
    }
  }

  void _handleMessage(Map<String, dynamic> data) {
    switch (data['type']) {
      case 'new_message':
        // Handle new message
        break;
      case 'message_deleted':
        // Handle message deletion
        break;
      case 'user_online':
        // Handle user online status
        break;
    }
  }

  void disconnect() {
    _channel?.sink.close();
    _channel = null;
  }
}
```

### 3. Broadcast Message via API
```dart
Future<Map<String, dynamic>> broadcastMessage({
  required String type,
  required Map<String, dynamic> data,
  required int userId,
  int? chatId,
  int? groupId,
}) async {
  try {
    final response = await _dio.post('/ws/broadcast', data: {
      'type': type,
      'data': data,
      'user_id': userId,
      'chat_id': chatId,
      'group_id': groupId,
    });
    return response.data;
  } catch (e) {
    throw _handleError(e);
  }
}

// Response Format:
{
  "success": true,
  "message": "Message broadcasted successfully"
}
```

## Error Handling

```dart
String _handleError(dynamic error) {
  if (error is DioException) {
    switch (error.type) {
      case DioExceptionType.connectionTimeout:
        return 'Connection timeout';
      case DioExceptionType.receiveTimeout:
        return 'Receive timeout';
      case DioExceptionType.badResponse:
        if (error.response?.data != null) {
          final data = error.response!.data;
          if (data is Map && data.containsKey('message')) {
            return data['message'];
          }
          if (data is Map && data.containsKey('errors')) {
            final errors = data['errors'] as Map;
            return errors.values.first[0];
          }
        }
        return 'Server error: ${error.response?.statusCode}';
      default:
        return 'Network error';
    }
  }
  return error.toString();
}
```

## Complete Example Usage

```dart
class ChatService {
  final ApiClient _apiClient = ApiClient();
  final WebSocketService _webSocketService = WebSocketService();

  Future<void> initialize() async {
    // Login first
    final loginResponse = await _apiClient.login(
      email: 'user@example.com',
      password: 'password',
    );
    
    // Set token
    _apiClient.setToken(loginResponse['token']);
    
    // Connect to WebSocket
    final wsResponse = await _apiClient.getWebSocketConnection();
    await _webSocketService.connect(
      wsResponse['websocket_url'],
      wsResponse['user_id'],
    );
  }

  Future<void> sendMessage(int receiverId, String message) async {
    // Send via API
    final response = await _apiClient.sendMessage(
      receiverId: receiverId,
      message: message,
    );
    
    // Broadcast via WebSocket
    await _apiClient.broadcastMessage(
      type: 'new_message',
      data: response['data'],
      userId: response['data']['sender_id'],
      chatId: response['data']['chat_id'],
    );
  }
}
```

## Installation Requirements

Add to `pubspec.yaml`:
```yaml
dependencies:
  dio: ^5.3.2
  web_socket_channel: ^2.4.0
```

## Server Setup

1. Install WebSocket server dependencies:
```bash
npm install express cors ws
```

2. Start WebSocket server:
```bash
node websocket-server.js
```

3. Start Laravel server:
```bash
php artisan serve
```