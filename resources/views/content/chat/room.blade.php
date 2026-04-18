@extends('layouts/layoutMaster')

@section('title', 'Project Chat - ' . ($project->title ?? 'Communication Center'))

@section('vendor-style')
    @vite('resources/assets/vendor/libs/maxLength/maxLength.scss')
@endsection

@section('page-style')
    @vite('resources/assets/vendor/scss/pages/app-chat.scss')
@endsection

@section('page-script')
    {{-- CRITICAL: Load app.js first to initialize window.Echo --}}
    @vite(['resources/js/app.js'])

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // 1. Single declaration of Project ID - Using $project->project_id from Controller
            const currentChatProjectId = "{{ $project->project_id ?? '' }}";
            
            if (!currentChatProjectId) {
                console.warn("No active project ID found.");
                return;
            }

            console.log("Checking Project ID:", currentChatProjectId);

            // 2. Auto-scroll to bottom on load
            const chatHistoryBody = document.querySelector('.chat-history-body');
            const chatHistoryList = document.querySelector('.chat-history');
            const sendForm = document.querySelector('.form-send-message');

            if (chatHistoryBody) {
                chatHistoryBody.scrollTop = chatHistoryBody.scrollHeight;
            }

            // --- 3. Real-Time Listener ---
            if (window.Echo) {
                console.log("Echo is active. Listening on: chat." + currentChatProjectId);
                
                window.Echo.private(`chat.${currentChatProjectId}`)
                .listen('.MessageSent', (e) => { // Removed full namespace to match broadcastAs default or manual naming
                    console.log("Real-time message received!", e);
                    appendMessage(e.message, false);
                });
            } else {
                console.error("Laravel Echo is not initialized. Check app.js and ensure 'npm run dev' is running.");
            }

            // --- 4. AJAX Message Sending ---
            if (sendForm) {
                sendForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const input = this.querySelector('input[name="message"]');
                    const messageText = input.value.trim();
                    if (!messageText) return;

                    axios.post(this.action, { message: messageText })
                        .then(response => {
                            // Append our own message immediately
                            appendMessage(response.data.message, true);
                            input.value = '';
                        })
                        .catch(error => {
                            console.error("AJAX Error:", error);
                        });
                });
            }

            // --- 5. Helper Function to Append HTML ---
            function appendMessage(msg, isMe) {
                if (!chatHistoryList) return;
                
                // Prevent duplicate messages if broadcast returns our own message
                if (!isMe && document.getElementById(`msg-${msg.id}`)) return;

                const alignment = isMe ? 'chat-message-right' : '';
                const senderName = msg.sender ? msg.sender.name : 'U';
    const firstLetter = senderName.charAt(0).toUpperCase();

    const messageHtml = `
        <li class="chat-message ${isMe ? 'chat-message-right' : ''}">
            <div class="d-flex overflow-hidden">
                <div class="user-avatar flex-shrink-0">
                    <div class="avatar avatar-sm">
                        <span class="avatar-initial rounded-circle bg-label-success">
                            ${firstLetter}
                        </span>
                    </div>
                </div>
                <div class="chat-message-wrapper flex-grow-1">
                    <div class="chat-message-text">
                        <p class="mb-0">${msg.message}</p>
                    </div>
                </div>
            </div>
        </li>`;
                
                chatHistoryList.insertAdjacentHTML('beforeend', messageHtml);
                if (chatHistoryBody) {
                    chatHistoryBody.scrollTop = chatHistoryBody.scrollHeight;
                }
            }
        });
    </script>
@endsection

@section('content')
@php
    $currentUserType = null;
    $currentUserId = null;

    if (auth()->guard('admin')->check() || session()->has('admin_id')) {
        $currentUserType = \App\Models\AdminAccount::class;
        $currentUserId = auth()->guard('admin')->id() ?? session('admin_id');
    } elseif (auth()->guard('manager')->check() || session()->has('manager_id')) {
        $currentUserType = \App\Models\ManagersAccount::class;
        $currentUserId = auth()->guard('manager')->id() ?? session('manager_id');
    } elseif (auth()->guard('intern')->check() || session()->has('int_id')) {
        $currentUserType = \App\Models\InternAccount::class;
        $currentUserId = auth()->guard('intern')->id() ?? session('int_id');
    }
@endphp

<div class="app-chat card overflow-hidden">
    <div class="row g-0">
        {{-- Sidebar --}}
        <div class="col app-chat-contacts app-sidebar flex-grow-0 overflow-hidden border-end show" id="app-chat-contacts">
            <div class="sidebar-header h-px-75 px-5 border-bottom d-flex align-items-center">
                <h5 class="m-0 text-primary fw-bold">Projects</h5>
            </div>
            <div class="sidebar-body" style="overflow-y: auto; height: calc(100vh - 300px);">
                <ul class="list-unstyled chat-contact-list py-2 mb-0">
                    @forelse($projects as $p)
                        {{-- Fixed projectId check by using $project->project_id --}}
                        <li class="chat-contact-list-item {{ isset($project) && $p->project_id == $project->project_id ? 'active' : '' }}">
                            <a href="{{ route('chat.show', $p->project_id) }}" class="d-flex align-items-center">
                                <div class="flex-shrink-0 avatar">
                                    <span class="avatar-initial rounded-circle bg-label-success">
                                        {{ substr($p->title, 0, 2) }}
                                    </span>
                                </div>
                                <div class="chat-contact-info flex-grow-1 ms-2">
                                    <h6 class="chat-contact-name text-truncate m-0">{{ $p->title }}</h6>
                                </div>
                            </a>
                        </li>
                    @empty
                        <li class="p-3 text-center text-muted">No projects found.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        {{-- Chat History --}}
        <div class="col app-chat-history bg-body">
            @if(isset($project))
                <div class="chat-history-wrapper">
                    <div class="chat-history-header border-bottom">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 avatar">
                                <span class="avatar-initial rounded-circle bg-label-primary">{{ substr($project->title, 0, 2) }}</span>
                            </div>
                            <div class="ms-3">
                                <h6 class="m-0 fw-bold">{{ $project->title }}</h6>
                                <small class="text-muted">Assigned to: {{ $project->intern->name ?? 'N/A' }}</small>
                            </div>
                        </div>
                    </div>

                    <div class="chat-history-body bg-body">
                        <ul class="list-unstyled chat-history">
                            @foreach($messages as $msg)
                                @php
                                    $isMe = ($msg->sender_type === $currentUserType && $msg->sender_id === $currentUserId);
                                    $role = 'Intern';
                                    if ($msg->sender_type === \App\Models\AdminAccount::class) $role = 'Admin';
                                    elseif ($msg->sender_type === \App\Models\ManagersAccount::class) $role = 'Manager';
                                @endphp
                                <li class="chat-message {{ $isMe ? 'chat-message-right' : '' }}" id="msg-{{ $msg->id }}">
                                    <div class="d-flex overflow-hidden">
                                        <div class="user-avatar flex-shrink-0 {{ $isMe ? 'ms-3' : 'me-3' }}">
                                            <div class="avatar avatar-sm">
                                                <span class="avatar-initial rounded-circle bg-label-{{ $role == 'Admin' ? 'danger' : ($role == 'Intern' ? 'info' : 'success') }}">
                                                    {{ substr($msg->sender->name ?? 'U', 0, 1) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="chat-message-wrapper flex-grow-1">
                                            <div class="chat-message-text">
                                                <p class="mb-0">{{ $msg->message }}</p>
                                            </div>
                                            <div class="text-muted mt-1">
                                                <small>{{ $msg->sender->name ?? 'Unknown' }} • {{ $msg->created_at->format('h:i A') }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="chat-history-footer border-top">
                        <form class="form-send-message d-flex" action="{{ route('chat.send', $project->project_id) }}" method="POST">
                            @csrf
                            <input class="form-control message-input border-0 me-3 shadow-none" name="message" placeholder="Type your message here..." autocomplete="off" required>
                            <button type="submit" class="btn btn-primary">
                                <i class="ti tabler-send"></i>
                                <span class="d-none d-md-inline ms-1">Send</span>
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div class="d-flex h-100 flex-column align-items-center justify-content-center text-center">
                    <i class="ti tabler-messages-off mb-3" style="font-size: 5rem; opacity: 0.2;"></i>
                    <h5>No Project Selected</h5>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection