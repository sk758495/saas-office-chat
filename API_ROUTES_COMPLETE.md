# Complete API Routes Documentation

## Public Routes (No Authentication Required)

### Authentication
- `POST /api/register` - User registration
- `POST /api/login` - User login
- `POST /api/forgot-password` - Send password reset OTP
- `POST /api/reset-password` - Reset password with OTP

### Public Data
- `GET /api/public/departments` - Get all departments
- `GET /api/public/designations/department/{departmentId}` - Get designations by department

## Protected Routes (Requires Authentication Token)

### Authentication & User
- `POST /api/logout` - User logout
- `POST /api/verify-otp` - Verify email OTP
- `POST /api/resend-otp` - Resend email verification OTP
- `GET /api/me` - Get current user info

### Chat & Messaging
- `GET /api/chat` - Get all chats (users and groups)
- `GET /api/chat/{user}` - Get chat messages with specific user
- `POST /api/chat/send` - Send message to user
- `GET /api/messages` - Get all messages (paginated)
- `GET /api/messages/{id}` - Get single message
- `DELETE /api/messages/{id}` - Delete message
- `GET /api/files/view/{id}` - View/display file from message
- `GET /api/files/download/{id}` - Download file from message
- `GET /api/users` - Get all users for chat
- `GET /api/unread-counts` - Get unread message counts for users
- `GET /api/group-unread-counts` - Get unread message counts for groups

### Groups
- `GET /api/groups` - Get user's groups
- `POST /api/groups` - Create new group
- `GET /api/groups/{id}` - Get group details and messages
- `POST /api/groups/send-message` - Send message to group
- `POST /api/groups/{id}/add-member` - Add member to group
- `POST /api/groups/{id}/remove-member` - Remove member from group
- `POST /api/groups/{id}/make-admin` - Make user group admin
- `POST /api/groups/{id}/photo` - Update group photo
- `POST /api/groups/{id}/exit` - Exit from group

### Profile Management
- `GET /api/profile` - Get user profile
- `POST /api/profile/photo` - Update profile photo
- `POST /api/profile/pin` - Set/Update PIN
- `POST /api/profile/toggle-lock` - Toggle PIN lock
- `POST /api/profile/verify-pin` - Verify PIN
- `POST /api/profile/forgot-pin` - Forgot PIN (send OTP)
- `POST /api/profile/verify-otp` - Verify PIN reset OTP
- `POST /api/profile/reset-pin-after-otp` - Reset PIN after OTP verification
- `POST /api/profile/remove-pin` - Remove PIN
- `POST /api/profile/send-current-email-verification` - Send current email verification
- `POST /api/profile/verify-current-email` - Verify current email
- `POST /api/profile/email` - Update email
- `POST /api/profile/verify-new-email` - Verify new email
- `POST /api/profile/password` - Update password

## Admin Routes (Requires Admin Authentication)

### Dashboard
- `GET /api/admin/dashboard` - Admin dashboard data

### Department Management
- `GET /api/admin/departments` - Get all departments
- `POST /api/admin/departments` - Create department
- `PUT /api/admin/departments/{department}` - Update department
- `DELETE /api/admin/departments/{department}` - Delete department

### Designation Management
- `GET /api/admin/designations` - Get all designations
- `POST /api/admin/designations` - Create designation
- `PUT /api/admin/designations/{designation}` - Update designation
- `DELETE /api/admin/designations/{designation}` - Delete designation
- `GET /api/admin/designations/department/{departmentId}` - Get designations by department

### Chat Monitoring
- `GET /api/admin/chat-monitor` - Get chat monitoring data
- `GET /api/admin/chat-monitor/{type}/{id}` - Get specific chat details

### User Management
- `GET /api/admin/users` - Get all users
- `GET /api/admin/users/{id}` - Get user details

### Data Export
- `GET /api/admin/export/users` - Export users data
- `GET /api/admin/export/one-to-one-chats` - Export one-to-one chats
- `GET /api/admin/export/group-chats` - Export group chats
- `GET /api/admin/export/departments` - Export departments

## Request Examples

### Send Message
```json
POST /api/chat/send
{
    "receiver_id": 2,
    "message": "Hello there!",
    "file": null // or file upload
}
```

### Forgot Password
```json
POST /api/forgot-password
{
    "email": "user@example.com"
}
```

### Reset Password
```json
POST /api/reset-password
{
    "email": "user@example.com",
    "otp": "123456",
    "password": "newpassword123",
    "password_confirmation": "newpassword123"
}
```

### Create Group
```json
POST /api/groups
{
    "name": "Project Team",
    "description": "Team discussion group",
    "member_ids": [2, 3, 4]
}
```

All protected routes require the Authorization header:
```
Authorization: Bearer {your_token_here}
```