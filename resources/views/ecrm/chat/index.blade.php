@extends('layouts.app')

@section('title', 'Chat - e-CRM')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white p-6 animate-fade-in">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <h1 class="text-2xl font-bold mb-2">Chat Pesanan</h1>
                    <div class="flex items-center gap-4 text-blue-100">
                        <div>
                            <span class="text-sm">Pesanan:</span>
                            <span class="font-semibold ml-1">{{ $order->nomor_order }}</span>
                        </div>
                        <span class="text-blue-300">•</span>
                        <div>
                            <span class="text-sm">Jenis:</span>
                            <span class="font-semibold ml-1">{{ ucfirst(str_replace('_', ' ', $order->jenis_desain)) }}</span>
                        </div>
                        <span class="text-blue-300">•</span>
                        <div>
                            <span class="text-sm">Klien:</span>
                            <span class="font-semibold ml-1">{{ $order->client->nama }}</span>
                        </div>
                    </div>
                </div>
                <a href="{{ route('ecrm.orders.show', $order) }}" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-4 py-2 rounded-lg transition-all duration-200 hover:scale-105 active:scale-95">
                    ← Kembali ke Pesanan
                </a>
            </div>
        </div>

        <!-- Chat Messages Container -->
        <div id="chat-messages" class="h-[500px] overflow-y-auto p-6 bg-gray-50 space-y-4">
            @forelse($messages->reverse() as $index => $message)
                <div class="flex {{ $message->user_id === Auth::id() ? 'justify-end' : 'justify-start' }} animate-slide-in-{{ $message->user_id === Auth::id() ? 'right' : 'left' }}" style="animation-delay: {{ $index * 0.05 }}s; opacity: 0; animation-fill-mode: forwards;">
                    <div class="flex gap-3 max-w-2xl {{ $message->user_id === Auth::id() ? 'flex-row-reverse' : '' }}">
                        <!-- Avatar -->
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-semibold text-sm transition-all duration-200 hover:scale-110
                                {{ $message->user->role === 'admin' ? 'bg-red-500' : 
                                   ($message->user->role === 'cs' ? 'bg-green-500' : 'bg-blue-500') }}">
                                {{ strtoupper(substr($message->user->name, 0, 1)) }}
                            </div>
                        </div>
                        
                        <!-- Message Bubble -->
                        <div class="flex flex-col {{ $message->user_id === Auth::id() ? 'items-end' : 'items-start' }}">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="font-semibold text-sm text-gray-700">
                                    {{ $message->user->name }}
                                </span>
                                <span class="text-xs text-gray-500">
                                    {{ $message->user->role === 'admin' ? 'Admin' : 
                                       ($message->user->role === 'cs' ? 'CS' : 'Klien') }}
                                </span>
                                @if($message->quickReply)
                                    <span class="text-xs bg-green-500 text-white px-2 py-0.5 rounded transition-all duration-200 hover:scale-110">Balasan Cepat</span>
                                @endif
                            </div>
                            
                            <div class="rounded-lg px-4 py-3 shadow-sm transition-all duration-200 hover:shadow-md
                                {{ $message->user_id === Auth::id() ? 'bg-blue-600 text-white' : 'bg-white text-gray-800 border border-gray-200' }}">
                                <p class="text-sm whitespace-pre-wrap leading-relaxed">{{ $message->pesan }}</p>
                            </div>
                            
                            <span class="text-xs text-gray-500 mt-1">
                                {{ $message->created_at->format('d M Y, H:i') }}
                            </span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                    </svg>
                    <p class="text-gray-500 font-medium">Belum ada pesan</p>
                    <p class="text-sm text-gray-400 mt-1">Mulai percakapan dengan mengirim pesan di bawah</p>
                </div>
            @endforelse
        </div>

        <!-- Quick Replies (for Admin only) -->
        @if(Auth::user()->role === 'admin' && $quickReplies->count() > 0)
        <div class="p-4 bg-gray-50 border-t border-gray-200">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">Balasan Cepat:</h3>
            <div class="flex flex-wrap gap-2">
                @foreach($quickReplies as $quickReply)
                    <form action="{{ route('ecrm.chat.quick-reply', $order) }}" method="POST" class="inline">
                        @csrf
                        <input type="hidden" name="quick_reply_id" value="{{ $quickReply->id }}">
                        <button type="submit" class="bg-white border border-gray-300 text-gray-700 px-3 py-1.5 rounded-lg text-sm hover:bg-gray-100 transition-all duration-200 hover:scale-105 active:scale-95">
                            {{ $quickReply->pertanyaan }}
                        </button>
                    </form>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Quick Replies (for Client) -->
        @if(Auth::user()->role === 'client' && $quickReplies->count() > 0)
        <div class="p-4 bg-gray-50 border-t border-gray-200">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">Pertanyaan Cepat:</h3>
            <div class="flex flex-wrap gap-2">
                @foreach($quickReplies as $quickReply)
                    <form action="{{ route('ecrm.chat.quick-reply', $order) }}" method="POST" class="inline">
                        @csrf
                        <input type="hidden" name="quick_reply_id" value="{{ $quickReply->id }}">
                        <button type="submit" class="bg-white border border-gray-300 text-gray-700 px-3 py-1.5 rounded-lg text-sm hover:bg-gray-100 transition-all duration-200 hover:scale-105 active:scale-95">
                            {{ $quickReply->pertanyaan }}
                        </button>
                    </form>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Send Message -->
        <div class="p-4 bg-white border-t border-gray-200">
            <form id="chat-form" action="{{ route('ecrm.chat.send', $order) }}" method="POST" class="flex gap-3">
                @csrf
                <textarea 
                    id="message-input"
                    name="pesan" 
                    rows="2" 
                    placeholder="Tulis pesan..." 
                    class="flex-1 border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none transition-all duration-200 focus:scale-105" 
                    required></textarea>
                <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-all duration-200 hover:scale-105 active:scale-95 ripple font-medium flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                    Kirim
                </button>
            </form>
        </div>
    </div>
</div>

<script>
// Auto scroll to bottom
function scrollToBottom() {
    const chatMessages = document.getElementById('chat-messages');
    if (chatMessages) {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
}

// Auto refresh chat every 5 seconds
let lastMessageId = {{ $messages->count() > 0 ? $messages->first()->id : 0 }};
let autoRefreshInterval;

function checkNewMessages() {
    fetch('{{ route('ecrm.chat.index', $order) }}?check_new=1&last_id=' + lastMessageId, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
        .then(response => response.json())
        .then(data => {
            if (data.has_new && data.messages && data.messages.length > 0) {
                // Reload page to show new messages
                location.reload();
            }
        })
        .catch(error => {
            // Silently fail - don't spam console
        });
}

document.addEventListener('DOMContentLoaded', function() {
    scrollToBottom();
    
    // Start auto-refresh
    autoRefreshInterval = setInterval(checkNewMessages, 5000);
    
    // Clear interval when page is hidden
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            clearInterval(autoRefreshInterval);
        } else {
            autoRefreshInterval = setInterval(checkNewMessages, 5000);
        }
    });
    
    // Form submission
    const chatForm = document.getElementById('chat-form');
    const messageInput = document.getElementById('message-input');
    
    if (chatForm) {
        chatForm.addEventListener('submit', function(e) {
            const message = messageInput.value.trim();
            if (!message) {
                e.preventDefault();
                return false;
            }
        });
    }
});

// Scroll to bottom when new messages arrive
const observer = new MutationObserver(function(mutations) {
    scrollToBottom();
});

const chatMessages = document.getElementById('chat-messages');
if (chatMessages) {
    observer.observe(chatMessages, { childList: true, subtree: true });
}
</script>
@endsection
