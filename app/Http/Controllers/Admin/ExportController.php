<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Group;
use App\Models\Message;
use App\Models\User;
use App\Models\Department;
use App\Models\Designation;
use Barryvdh\DomPDF\Facade\Pdf;

class ExportController extends Controller
{
    public function exportData($type)
    {
        switch ($type) {
            case 'users':
                return $this->exportUsers();
            case 'chats':
                return $this->exportChats();
            case 'one-to-one-chats':
                return $this->exportOneToOneChats();
            case 'group-chats':
                return $this->exportGroupChats();
            case 'messages':
                return $this->exportMessages();
            case 'departments':
                return $this->exportDepartments();
            default:
                return redirect()->back()->with('error', 'Invalid export type');
        }
    }

    private function exportUsers()
    {
        $users = User::with(['department', 'designation'])->get();
        $pdf = Pdf::loadView('admin.exports.users', compact('users'));
        return $pdf->download('users-report-' . date('Y-m-d') . '.pdf');
    }

    private function exportChats()
    {
        $chats = Chat::with(['user1', 'user2'])->get();
        $groups = Group::with('members')->get();
        $pdf = Pdf::loadView('admin.exports.chats', compact('chats', 'groups'));
        return $pdf->download('chats-report-' . date('Y-m-d') . '.pdf');
    }

    private function exportMessages()
    {
        $messages = Message::with(['sender', 'chat.user1', 'chat.user2', 'group'])->latest()->limit(1000)->get();
        $pdf = Pdf::loadView('admin.exports.messages', compact('messages'));
        return $pdf->download('messages-report-' . date('Y-m-d') . '.pdf');
    }

    private function exportOneToOneChats()
    {
        $chats = Chat::with(['user1', 'user2', 'messages.sender'])->get();
        $pdf = Pdf::loadView('admin.exports.one-to-one-chats', compact('chats'));
        return $pdf->download('one-to-one-chats-' . date('Y-m-d') . '.pdf');
    }

    private function exportGroupChats()
    {
        $groups = Group::with(['members', 'messages.sender'])->get();
        $pdf = Pdf::loadView('admin.exports.group-chats', compact('groups'));
        return $pdf->download('group-chats-' . date('Y-m-d') . '.pdf');
    }

    private function exportDepartments()
    {
        $departments = Department::withCount('users')->get();
        $designations = Designation::with('department')->get();
        $pdf = Pdf::loadView('admin.exports.departments', compact('departments', 'designations'));
        return $pdf->download('departments-report-' . date('Y-m-d') . '.pdf');
    }
}