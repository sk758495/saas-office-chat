<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Designation;
use App\Models\User;
use App\Models\Chat;
use App\Models\Group;
use App\Models\Message;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // Dashboard
    public function dashboard()
    {
        $stats = [
            'departments' => Department::count(),
            'designations' => Designation::count(),
            'users' => User::count(),
            'messages' => Message::count(),
            'individual_chats' => Chat::count(),
            'group_chats' => Group::count()
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    // Departments
    public function getDepartments()
    {
        $departments = Department::latest()->get();
        
        return response()->json([
            'success' => true,
            'departments' => $departments
        ]);
    }

    public function createDepartment(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:departments',
        ]);

        $department = Department::create($request->all());
        
        return response()->json([
            'success' => true,
            'message' => 'Department created successfully',
            'department' => $department
        ], 201);
    }

    public function updateDepartment(Request $request, Department $department)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:departments,name,' . $department->id,
        ]);

        $department->update($request->all());
        
        return response()->json([
            'success' => true,
            'message' => 'Department updated successfully',
            'department' => $department
        ]);
    }

    public function deleteDepartment(Department $department)
    {
        $department->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Department deleted successfully'
        ]);
    }

    // Designations
    public function getDesignations()
    {
        $designations = Designation::with('department')->latest()->get();
        
        return response()->json([
            'success' => true,
            'designations' => $designations
        ]);
    }

    public function createDesignation(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
        ]);

        $designation = Designation::create($request->all());
        
        return response()->json([
            'success' => true,
            'message' => 'Designation created successfully',
            'designation' => $designation->load('department')
        ], 201);
    }

    public function updateDesignation(Request $request, Designation $designation)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
        ]);

        $designation->update($request->all());
        
        return response()->json([
            'success' => true,
            'message' => 'Designation updated successfully',
            'designation' => $designation->load('department')
        ]);
    }

    public function deleteDesignation(Designation $designation)
    {
        $designation->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Designation deleted successfully'
        ]);
    }

    public function getDesignationsByDepartment($departmentId)
    {
        $designations = Designation::where('department_id', $departmentId)
                                  ->where('status', true)
                                  ->get();
        
        return response()->json([
            'success' => true,
            'designations' => $designations
        ]);
    }

    // Chat Monitor
    public function getChatMonitor()
    {
        $individualChats = Chat::with(['user1', 'user2', 'lastMessage'])
                              ->orderBy('last_message_at', 'desc')
                              ->get();

        $groupChats = Group::with(['members', 'lastMessage'])
                          ->orderBy('updated_at', 'desc')
                          ->get();

        return response()->json([
            'success' => true,
            'individual_chats' => $individualChats,
            'group_chats' => $groupChats
        ]);
    }

    public function getChatDetails($type, $id)
    {
        if ($type === 'individual') {
            $chat = Chat::with(['user1', 'user2', 'messages.sender'])->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'type' => 'individual',
                'chat' => $chat,
                'messages' => $chat->messages()->with('sender')->orderBy('created_at')->get()
            ]);
        } else {
            $group = Group::with(['members', 'messages.sender'])->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'type' => 'group',
                'group' => $group,
                'messages' => $group->messages()->with('sender')->orderBy('created_at')->get()
            ]);
        }
    }

    // Users Management
    public function getUsers()
    {
        $users = User::with(['department', 'designation'])->latest()->get();
        
        return response()->json([
            'success' => true,
            'users' => $users
        ]);
    }

    public function getUserDetails($id)
    {
        $user = User::with(['department', 'designation'])->findOrFail($id);
        
        // Get user's chat statistics
        $chatStats = [
            'individual_chats' => Chat::where('user1_id', $id)->orWhere('user2_id', $id)->count(),
            'group_chats' => $user->groups()->count(),
            'messages_sent' => Message::where('sender_id', $id)->count(),
            'last_active' => $user->last_seen
        ];
        
        return response()->json([
            'success' => true,
            'user' => $user,
            'chat_stats' => $chatStats
        ]);
    }

    // Export Data
    public function exportUsers()
    {
        $users = User::with(['department', 'designation'])->get();
        
        return response()->json([
            'success' => true,
            'users' => $users,
            'export_type' => 'users',
            'generated_at' => now()
        ]);
    }

    public function exportOneToOneChats()
    {
        $chats = Chat::with(['user1', 'user2', 'messages.sender'])->get();
        
        return response()->json([
            'success' => true,
            'chats' => $chats,
            'export_type' => 'one_to_one_chats',
            'generated_at' => now()
        ]);
    }

    public function exportGroupChats()
    {
        $groups = Group::with(['members', 'messages.sender'])->get();
        
        return response()->json([
            'success' => true,
            'groups' => $groups,
            'export_type' => 'group_chats',
            'generated_at' => now()
        ]);
    }

    public function exportDepartments()
    {
        $departments = Department::withCount('users')->get();
        $designations = Designation::with('department')->get();
        
        return response()->json([
            'success' => true,
            'departments' => $departments,
            'designations' => $designations,
            'export_type' => 'departments',
            'generated_at' => now()
        ]);
    }
}