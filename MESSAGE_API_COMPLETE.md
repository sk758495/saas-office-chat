# Complete Message API Documentation

## 📨 Send Message APIs

### 1. Send Message to User
```http
POST /api/chat/send
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

**Request Body:**
```json
{
    "receiver_id": 2,
    "message": "Hello there!",
    "file": null // or file upload
}
```

**Response:**
```json
{
    "success": true,
    "message": {
        "id": 1,
        "chat_id": 1,
        "sender_id": 1,
        "message": "Hello there!",
        "type": "text",
        "created_at": "2024-01-01T10:00:00Z",
        "sender": {
            "id": 1,
            "name": "John Doe"
        }
    }
}
```

### 2. Send Message to Group
```http
POST /api/groups/send-message
Authorization: Bearer {token}
```

**Request Body:**
```json
{
    "group_id": 1,
    "message": "Hello group!",
    "file": null // or file upload
}
```

## 📥 Get Messages APIs

### 1. Get All User Chats & Groups
```http
GET /api/chat
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "users": [
        {
            "id": 2,
            "name": "Jane Doe",
            "email": "jane@example.com",
            "unread_count": 3,
            "last_message_at": "2024-01-01T10:00:00Z"
        }
    ],
    "groups": [
        {
            "id": 1,
            "name": "Project Team",
            "unread_count": 5,
            "last_message_at": "2024-01-01T09:30:00Z"
        }
    ]
}
```

### 2. Get Messages with Specific User
```http
GET /api/chat/{user_id}
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "chat": {
        "id": 1,
        "user1_id": 1,
        "user2_id": 2
    },
    "messages": [
        {
            "id": 1,
            "message": "Hello!",
            "type": "text",
            "sender_id": 1,
            "created_at": "2024-01-01T10:00:00Z",
            "sender": {
                "id": 1,
                "name": "John Doe"
            }
        }
    ],
    "otherUser": {
        "id": 2,
        "name": "Jane Doe",
        "department": {...},
        "designation": {...}
    }
}
```

### 3. Get Group Messages
```http
GET /api/groups/{group_id}
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "group": {
        "id": 1,
        "name": "Project Team",
        "description": "Team discussion"
    },
    "messages": [
        {
            "id": 1,
            "message": "Hello group!",
            "type": "text",
            "sender_id": 1,
            "group_id": 1,
            "created_at": "2024-01-01T10:00:00Z",
            "sender": {
                "id": 1,
                "name": "John Doe"
            }
        }
    ]
}
```

### 4. Get All Messages (Paginated)
```http
GET /api/messages?page=1
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "messages": {
        "data": [
            {
                "id": 1,
                "message": "Hello!",
                "type": "text",
                "sender": {...},
                "chat": {...},
                "group": null,
                "created_at": "2024-01-01T10:00:00Z"
            }
        ],
        "current_page": 1,
        "per_page": 50,
        "total": 100
    }
}
```

### 5. Get Single Message
```http
GET /api/messages/{message_id}
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "message": {
        "id": 1,
        "message": "Hello!",
        "type": "text",
        "file_path": null,
        "sender": {
            "id": 1,
            "name": "John Doe"
        },
        "chat": {...},
        "group": null,
        "created_at": "2024-01-01T10:00:00Z"
    }
}
```

## 🗑️ Delete Message API

### Delete Message
```http
DELETE /api/messages/{message_id}
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "message": "Message deleted successfully"
}
```

## 📊 Message Status APIs

### 1. Get Unread Counts (Users)
```http
GET /api/unread-counts
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "unread_counts": {
        "2": 3,
        "3": 1
    }
}
```

### 2. Get Group Unread Counts
```http
GET /api/group-unread-counts
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "unread_counts": {
        "1": 5,
        "2": 2
    }
}
```

## 📎 File Upload Support

All message APIs support file uploads:

**Supported File Types:**
- Images: jpg, jpeg, png, gif
- Documents: pdf, doc, docx, txt
- Archives: zip (auto-extracts folder contents)
- Any file up to 50MB

**File Upload Example:**
```javascript
const formData = new FormData();
formData.append('receiver_id', '2');
formData.append('message', 'Check this file!');
formData.append('file', fileInput.files[0]);

fetch('/api/chat/send', {
    method: 'POST',
    headers: {
        'Authorization': 'Bearer ' + token
    },
    body: formData
});
```

## 🔐 Authentication

All message APIs require authentication token in header:
```
Authorization: Bearer {your_token_here}
```

## ✅ Complete Message Flow

1. **Login**: `POST /api/login`
2. **Get Chats**: `GET /api/chat`
3. **Open Chat**: `GET /api/chat/{user_id}`
4. **Send Message**: `POST /api/chat/send`
5. **Check Unread**: `GET /api/unread-counts`
6. **Delete Message**: `DELETE /api/messages/{id}`