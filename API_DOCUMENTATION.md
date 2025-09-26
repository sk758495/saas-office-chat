# Office Chat API Documentation

## Base URL
```
http://your-domain.com/api
```

## Authentication
All protected routes require Bearer token in header:
```
Authorization: Bearer {your_token}
```

---

## 🔐 Authentication Endpoints

### Register User
**POST** `/register`

**Body:**
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "mobile": "1234567890",
    "department_id": 1,
    "designation_id": 1
}
```

**Response:**
```json
{
    "success": true,
    "message": "Registration successful. Please verify your email.",
    "user": {...},
    "token": "your_token_here",
    "token_type": "Bearer"
}
```

### Login
**POST** `/login`

**Body:**
```json
{
    "email": "john@example.com",
    "password": "password123"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Login successful",
    "user": {...},
    "token": "your_token_here",
    "token_type": "Bearer"
}
```

### Logout
**POST** `/logout`
*Requires Authentication*

**Response:**
```json
{
    "success": true,
    "message": "Logged out successfully"
}
```

### Verify OTP
**POST** `/verify-otp`
*Requires Authentication*

**Body:**
```json
{
    "otp": "123456"
}
```

### Resend OTP
**POST** `/resend-otp`
*Requires Authentication*

### Get Current User
**GET** `/me`
*Requires Authentication*

---

## 💬 Chat Endpoints

### Get Chat List
**GET** `/chat`
*Requires Authentication*

**Response:**
```json
{
    "success": true,
    "users": [...],
    "groups": [...]
}
```

### Get Chat Messages
**GET** `/chat/{user_id}`
*Requires Authentication*

**Response:**
```json
{
    "success": true,
    "chat": {...},
    "messages": [...],
    "otherUser": {...}
}
```

### Send Message
**POST** `/chat/send`
*Requires Authentication*

**Body (Text Message):**
```json
{
    "receiver_id": 2,
    "message": "Hello there!"
}
```

**Body (File Upload):**
```form-data
receiver_id: 2
message: "Check this file"
file: [file]
```

**Body (Folder Upload):**
```form-data
receiver_id: 2
folder_files[]: [file1]
folder_files[]: [file2]
folder_name: "My Folder"
```

### Get Users List
**GET** `/users`
*Requires Authentication*

### Get Unread Counts
**GET** `/unread-counts`
*Requires Authentication*

### Get Group Unread Counts
**GET** `/group-unread-counts`
*Requires Authentication*

---

## 👥 Group Endpoints

### Get Groups
**GET** `/groups`
*Requires Authentication*

### Create Group
**POST** `/groups`
*Requires Authentication*

**Body:**
```json
{
    "name": "Project Team",
    "description": "Team discussion group",
    "members": [2, 3, 4]
}
```

### Get Group Details
**GET** `/groups/{group_id}`
*Requires Authentication*

### Send Group Message
**POST** `/groups/send-message`
*Requires Authentication*

**Body:**
```json
{
    "group_id": 1,
    "message": "Hello everyone!"
}
```

### Add Member
**POST** `/groups/{group_id}/add-member`
*Requires Authentication*

**Body:**
```json
{
    "user_id": 5
}
```

### Remove Member
**POST** `/groups/{group_id}/remove-member`
*Requires Authentication*

**Body:**
```json
{
    "user_id": 5
}
```

### Make Admin
**POST** `/groups/{group_id}/make-admin`
*Requires Authentication*

**Body:**
```json
{
    "user_id": 5
}
```

### Update Group Photo
**POST** `/groups/{group_id}/photo`
*Requires Authentication*

**Body (Form Data):**
```form-data
profile_picture: [image_file]
```

### Exit Group
**POST** `/groups/{group_id}/exit`
*Requires Authentication*

---

## 👤 Profile Endpoints

### Get Profile
**GET** `/profile`
*Requires Authentication*

### Update Profile Photo
**POST** `/profile/photo`
*Requires Authentication*

**Body (Form Data):**
```form-data
profile_photo: [image_file]
```

### Set/Update PIN
**POST** `/profile/pin`
*Requires Authentication*

**Body:**
```json
{
    "pin": "1234",
    "confirm_pin": "1234"
}
```

### Toggle Chat Lock
**POST** `/profile/toggle-lock`
*Requires Authentication*

### Verify PIN
**POST** `/profile/verify-pin`
*Requires Authentication*

**Body:**
```json
{
    "pin": "1234"
}
```

### Forgot PIN
**POST** `/profile/forgot-pin`
*Requires Authentication*

**Body:**
```json
{
    "email": "john@example.com"
}
```

### Verify OTP for PIN Reset
**POST** `/profile/verify-otp`
*Requires Authentication*

**Body:**
```json
{
    "otp": "123456"
}
```

### Reset PIN After OTP
**POST** `/profile/reset-pin-after-otp`
*Requires Authentication*

**Body:**
```json
{
    "new_pin": "5678",
    "confirm_pin": "5678",
    "otp": "123456"
}
```

### Remove PIN
**POST** `/profile/remove-pin`
*Requires Authentication*

### Send Current Email Verification
**POST** `/profile/send-current-email-verification`
*Requires Authentication*

### Verify Current Email
**POST** `/profile/verify-current-email`
*Requires Authentication*

**Body:**
```json
{
    "otp": "123456"
}
```

### Update Email
**POST** `/profile/email`
*Requires Authentication*

**Body:**
```json
{
    "email": "newemail@example.com",
    "current_email_otp": "123456"
}
```

### Verify New Email
**POST** `/profile/verify-new-email`
*Requires Authentication*

**Body:**
```json
{
    "otp": "123456"
}
```

### Update Password
**POST** `/profile/password`
*Requires Authentication*

**Body:**
```json
{
    "current_password": "oldpassword",
    "password": "newpassword",
    "password_confirmation": "newpassword",
    "current_email_otp": "123456"
}
```

---

## 🔧 Admin Endpoints
*All admin routes require admin authentication*

### Dashboard Stats
**GET** `/admin/dashboard`

**Response:**
```json
{
    "success": true,
    "stats": {
        "departments": 5,
        "designations": 15,
        "users": 50,
        "messages": 1000,
        "individual_chats": 25,
        "group_chats": 8
    }
}
```

### Departments
**GET** `/admin/departments`
**POST** `/admin/departments`
**PUT** `/admin/departments/{id}`
**DELETE** `/admin/departments/{id}`

**Create/Update Body:**
```json
{
    "name": "IT Department"
}
```

### Designations
**GET** `/admin/designations`
**POST** `/admin/designations`
**PUT** `/admin/designations/{id}`
**DELETE** `/admin/designations/{id}`

**Create/Update Body:**
```json
{
    "name": "Software Engineer",
    "department_id": 1
}
```

### Get Designations by Department
**GET** `/admin/designations/department/{department_id}`

### Chat Monitor
**GET** `/admin/chat-monitor`
**GET** `/admin/chat-monitor/{type}/{id}`

### Users Management
**GET** `/admin/users`
**GET** `/admin/users/{id}`

### Export Data
**GET** `/admin/export/users`
**GET** `/admin/export/one-to-one-chats`
**GET** `/admin/export/group-chats`
**GET** `/admin/export/departments`

---

## 🌐 Public Endpoints

### Get Departments (for registration)
**GET** `/public/departments`

### Get Designations by Department (for registration)
**GET** `/public/designations/department/{department_id}`

---

## 📝 Response Format

### Success Response
```json
{
    "success": true,
    "message": "Operation successful",
    "data": {...}
}
```

### Error Response
```json
{
    "success": false,
    "message": "Error message",
    "errors": {...}
}
```

---

## 🔍 Testing with Postman

### 1. Register User
- Method: POST
- URL: `http://your-domain.com/api/register`
- Body: raw JSON with user data

### 2. Login
- Method: POST  
- URL: `http://your-domain.com/api/login`
- Body: raw JSON with email/password

### 3. Use Token
- Copy token from login response
- Add to Headers: `Authorization: Bearer {token}`

### 4. Test Protected Routes
- Use token in all subsequent requests
- Test chat, groups, profile endpoints

---

## 📱 File Upload Examples

### Profile Photo Upload
```javascript
const formData = new FormData();
formData.append('profile_photo', file);

fetch('/api/profile/photo', {
    method: 'POST',
    headers: {
        'Authorization': 'Bearer ' + token
    },
    body: formData
});
```

### Chat File Upload
```javascript
const formData = new FormData();
formData.append('receiver_id', '2');
formData.append('message', 'Check this file');
formData.append('file', file);

fetch('/api/chat/send', {
    method: 'POST',
    headers: {
        'Authorization': 'Bearer ' + token
    },
    body: formData
});
```

---

## ⚠️ Important Notes

1. **Authentication Required**: Most endpoints require Bearer token
2. **File Uploads**: Use `multipart/form-data` for file uploads
3. **JSON Requests**: Use `application/json` for JSON data
4. **Error Handling**: Always check `success` field in response
5. **Rate Limiting**: API may have rate limits in production
6. **CORS**: Configure CORS for cross-origin requests

---

## 🚀 Quick Start

1. Register a new user
2. Login to get token
3. Use token for all protected requests
4. Test chat functionality
5. Create groups and send messages
6. Update profile settings

**Happy Coding! 🎉**