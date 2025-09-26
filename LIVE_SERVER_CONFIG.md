# Live Server API Configuration

## Flutter Base URL
```dart
class ApiClient {
  static const String baseUrl = 'http://office-chat.jashmainfosoft.com/api';
  
  late Dio _dio;

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
  }
}
```

## Android Network Config
**android/app/src/main/AndroidManifest.xml:**
```xml
<application
    android:usesCleartextTraffic="true">
```

## Test API Endpoints
- Login: `POST http://office-chat.jashmainfosoft.com/api/login`
- Register: `POST http://office-chat.jashmainfosoft.com/api/register`
- WebSocket: `GET http://office-chat.jashmainfosoft.com/api/ws/connect`

## Server Requirements
1. Ensure Laravel is running on port 80 (default HTTP)
2. Start WebSocket server: `node websocket-server.js`
3. Configure server to accept external connections