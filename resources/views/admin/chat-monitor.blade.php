@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Chat Monitor</h1>
        <p class="text-gray-600 mt-2">Monitor all conversations and group chats in your organization</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="orange-gradient p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-comment text-2xl text-white"></i>
                    </div>
                    <div>
                        <p class="text-orange-100 text-sm font-medium">Individual Chats</p>
                        <p class="text-3xl font-bold text-white">{{ $allChats->where('type', 'individual')->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="red-gradient p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-users text-2xl text-white"></i>
                    </div>
                    <div>
                        <p class="text-red-100 text-sm font-medium">Group Chats</p>
                        <p class="text-3xl font-bold text-white">{{ $allChats->where('type', 'group')->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-orange-500 to-red-500 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-envelope text-2xl text-white"></i>
                    </div>
                    <div>
                        <p class="text-orange-100 text-sm font-medium">Total Messages</p>
                        <p class="text-3xl font-bold text-white">{{ $allChats->sum('message_count') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chat List -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="orange-gradient px-6 py-4">
            <h2 class="text-xl font-bold text-white flex items-center">
                <i class="fas fa-comments mr-3"></i>All Conversations
            </h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Participants</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Message</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Messages</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Activity</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($allChats as $chat)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($chat['type'] === 'individual')
                                <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">
                                    <i class="fas fa-user mr-1"></i>Individual
                                </span>
                            @else
                                <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    <i class="fas fa-users mr-1"></i>Group
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $chat['participants'] }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-500 max-w-xs truncate">
                                {{ Str::limit($chat['last_message'], 50) }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                {{ $chat['message_count'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $chat['last_activity']->diffForHumans() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.chat-monitor.show', [$chat['type'], $chat['id']]) }}" 
                               class="orange-gradient text-white px-4 py-2 rounded-lg hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                                <i class="fas fa-eye mr-1"></i>View Chat
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-comments text-4xl text-gray-300 mb-4"></i>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">No conversations found</h3>
                                <p class="text-gray-500">Users haven't started chatting yet.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Export Options -->
    <div class="mt-8 bg-gradient-to-r from-orange-50 to-red-50 border border-orange-200 rounded-xl p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-download text-white"></i>
                </div>
                <div>
                    <h3 class="text-orange-800 font-bold text-lg">Export Chat Data</h3>
                    <p class="text-orange-600">Download conversation reports for analysis</p>
                </div>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.export', 'chats') }}" class="orange-gradient text-white px-4 py-2 rounded-lg hover:shadow-lg transition-all">
                    <i class="fas fa-file-excel mr-2"></i>Export All
                </a>
                <a href="{{ route('admin.export', 'one-to-one-chats') }}" class="red-gradient text-white px-4 py-2 rounded-lg hover:shadow-lg transition-all">
                    <i class="fas fa-user mr-2"></i>Individual Only
                </a>
                <a href="{{ route('admin.export', 'group-chats') }}" class="bg-gradient-to-r from-red-500 to-orange-500 text-white px-4 py-2 rounded-lg hover:shadow-lg transition-all">
                    <i class="fas fa-users mr-2"></i>Groups Only
                </a>
            </div>
        </div>
    </div>
</div>
@endsection