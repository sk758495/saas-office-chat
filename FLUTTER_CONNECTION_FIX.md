# Flutter Connection Fix

## Issue
Connection reset by peer error when connecting to `office-chat.jashmainfosoft.com`

## Solutions

### 1. Update Flutter Base URL
```dart
class ApiClient {
  // Try these URLs in order:
  static const String baseUrl = 'https://office-chat.jashmainfosoft.com/api'; // HTTPS first
  // static const String baseUrl = 'http://office-chat.jashmainfosoft.com/api'; // HTTP fallback
  // static const String baseUrl = 'http://127.0.0.1:8000/api'; // Local testing
  
  late Dio _dio;

  ApiClient() {
    _dio = Dio(BaseOptions(
      baseUrl: baseUrl,
      connectTimeout: Duration(seconds: 60), // Increased timeout
      receiveTimeout: Duration(seconds: 60),
      sendTimeout: Duration(seconds: 60),
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
    ));

    // Add retry interceptor
    _dio.interceptors.add(RetryInterceptor(
      dio: _dio,
      logPrint: print,
      retries: 3,
      retryDelays: [
        Duration(seconds: 1),
        Duration(seconds: 2),
        Duration(seconds: 3),
      ],
    ));
  }
}
```

### 2. Add Certificate Bypass (for HTTPS issues)
```dart
import 'dart:io';

class ApiClient {
  ApiClient() {
    // Bypass certificate validation (only for development)
    HttpOverrides.global = MyHttpOverrides();
    
    _dio = Dio(BaseOptions(
      baseUrl: baseUrl,
      connectTimeout: Duration(seconds: 60),
      receiveTimeout: Duration(seconds: 60),
    ));
  }
}

class MyHttpOverrides extends HttpOverrides {
  @override
  HttpClient createHttpClient(SecurityContext? context) {
    return super.createHttpClient(context)
      ..badCertificateCallback = (X509Certificate cert, String host, int port) => true;
  }
}
```

### 3. Enhanced Error Handling
```dart
Future<Map<String, dynamic>> login({
  required String email,
  required String password,
}) async {
  try {
    print('🔑 Attempting login to: ${_dio.options.baseUrl}/login');
    
    final response = await _dio.post('/login', 
      data: {
        'email': email,
        'password': password,
      },
      options: Options(
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
        },
      ),
    );
    
    print('✅ Login successful: ${response.statusCode}');
    return response.data;
    
  } on DioException catch (e) {
    print('❌ DioException: ${e.type}');
    print('❌ Message: ${e.message}');
    print('❌ Response: ${e.response?.data}');
    
    if (e.type == DioExceptionType.connectionError) {
      throw 'Connection failed. Please check your internet connection and server status.';
    } else if (e.type == DioExceptionType.connectionTimeout) {
      throw 'Connection timeout. Server may be slow or unreachable.';
    } else if (e.response?.statusCode == 401) {
      throw 'Invalid email or password.';
    } else {
      throw 'Login failed: ${e.message}';
    }
  } catch (e) {
    print('❌ Unexpected error: $e');
    throw 'Unexpected error occurred: $e';
  }
}
```

### 4. Server Status Check
```dart
Future<bool> checkServerStatus() async {
  try {
    final response = await _dio.get('/test-auth', 
      options: Options(
        sendTimeout: Duration(seconds: 10),
        receiveTimeout: Duration(seconds: 10),
      ),
    );
    return response.statusCode == 200;
  } catch (e) {
    print('Server status check failed: $e');
    return false;
  }
}
```

### 5. Network Configuration Check
Add to `android/app/src/main/AndroidManifest.xml`:
```xml
<application
    android:usesCleartextTraffic="true"
    android:networkSecurityConfig="@xml/network_security_config">
```

Create `android/app/src/main/res/xml/network_security_config.xml`:
```xml
<?xml version="1.0" encoding="utf-8"?>
<network-security-config>
    <domain-config cleartextTrafficPermitted="true">
        <domain includeSubdomains="true">office-chat.jashmainfosoft.com</domain>
        <domain includeSubdomains="true">127.0.0.1</domain>
        <domain includeSubdomains="true">localhost</domain>
    </domain-config>
</network-security-config>
```

### 6. Test Connection First
```dart
void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  
  final apiClient = ApiClient();
  
  // Test server connectivity
  print('🔍 Testing server connection...');
  final isServerUp = await apiClient.checkServerStatus();
  
  if (!isServerUp) {
    print('⚠️ Server is not reachable. Check server status.');
  } else {
    print('✅ Server is reachable.');
  }
  
  runApp(MyApp());
}
```

## Server-Side Fixes Applied

1. ✅ Added CORS configuration
2. ✅ Added CORS middleware
3. ✅ Added OPTIONS routes for preflight
4. ✅ Enhanced error responses
5. ✅ Added proper headers to all responses

## Troubleshooting Steps

1. **Check server status**: Ensure Laravel server is running
2. **Test with Postman**: Verify API endpoints work
3. **Check network**: Ensure device can reach the server
4. **Try different URLs**: HTTPS → HTTP → Local IP
5. **Check logs**: Monitor Laravel logs for errors
6. **Increase timeouts**: Server might be slow

## Dependencies
```yaml
dependencies:
  dio: ^5.3.2
  dio_retry_interceptor: ^1.0.0
```