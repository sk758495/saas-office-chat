# Quick Flutter Connection Fix

## 1. Change Base URL to HTTP
```dart
class ApiClient {
  // Change from HTTPS to HTTP
  static const String baseUrl = 'http://office-chat.jashmainfosoft.com/api';
  
  // If HTTP doesn't work, try with port
  // static const String baseUrl = 'http://office-chat.jashmainfosoft.com:8000/api';
}
```

## 2. Add Network Security Config (Android)

**android/app/src/main/AndroidManifest.xml:**
```xml
<application
    android:usesCleartextTraffic="true">
```

## 3. Test with Local IP First
```dart
// Test with local server first
static const String baseUrl = 'http://192.168.1.100:8000/api'; // Replace with actual server IP
```

## 4. Add Connection Timeout
```dart
ApiClient() {
  _dio = Dio(BaseOptions(
    baseUrl: baseUrl,
    connectTimeout: Duration(seconds: 30),
    receiveTimeout: Duration(seconds: 30),
  ));
}
```

## 5. Test Server Accessibility
Run this command to test if server is reachable:
```bash
curl -X POST http://office-chat.jashmainfosoft.com/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@test.com","password":"password"}'
```