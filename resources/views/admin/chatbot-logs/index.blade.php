@extends('layouts.admin')

@section('header', 'AI Chatbot Logs & Conversations')

@section('content')
<div x-data="{ activeLog: null }" class="space-y-6">
    <!-- Summary KPIs -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm">
            <span class="text-xs font-semibold text-slate-500">Total Messages</span>
            <p class="text-2xl font-bold text-slate-900 mt-1">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm">
            <span class="text-xs font-semibold text-slate-500">Answered</span>
            <p class="text-2xl font-bold text-emerald-700 mt-1">{{ $stats['answered'] }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm">
            <span class="text-xs font-semibold text-slate-500">Guardrail Blocked</span>
            <p class="text-2xl font-bold text-rose-600 mt-1">{{ $stats['blocked'] }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm">
            <span class="text-xs font-semibold text-slate-500">Chat Sessions</span>
            <p class="text-2xl font-bold text-chaouen-700 mt-1">{{ $stats['sessions'] }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm">
            <span class="text-xs font-semibold text-slate-500">Today</span>
            <p class="text-2xl font-bold text-sky-600 mt-1">{{ $stats['today'] }}</p>
        </div>
    </div>

    <!-- Toolbar & Filters -->
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.chatbot-logs.index', array_merge(request()->except('view', 'page', 'session_id'), ['view' => 'messages'])) }}"
                   class="px-4 py-2 rounded-xl text-xs font-bold {{ ($viewMode !== 'sessions' && $selectedSession === '') ? 'bg-chaouen-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    💬 All Messages
                </a>
                <a href="{{ route('admin.chatbot-logs.index', array_merge(request()->except('view', 'page', 'session_id'), ['view' => 'sessions'])) }}"
                   class="px-4 py-2 rounded-xl text-xs font-bold {{ ($viewMode === 'sessions' && $selectedSession === '') ? 'bg-chaouen-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    🗂️ Grouped by Session
                </a>
                @if($selectedSession !== '')
                    <a href="{{ route('admin.chatbot-logs.index') }}" class="px-4 py-2 rounded-xl text-xs font-bold bg-amber-100 text-amber-900">
                        ← Back to All Logs
                    </a>
                @endif
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.chatbot-logs.export', request()->query()) }}"
                   class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold">
                    ⬇ Export CSV
                </a>
                @if($stats['blocked'] > 0)
                    <form action="{{ route('admin.chatbot-logs.clear') }}" method="POST" onsubmit="return confirm('Delete all guardrail-blocked chatbot logs?')">
                        @csrf @method('DELETE')
                        <input type="hidden" name="scope" value="blocked">
                        <button type="submit" class="px-3.5 py-2 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 text-xs font-bold">
                            Clear Blocked ({{ $stats['blocked'] }})
                        </button>
                    </form>
                @endif
                @if($stats['total'] > 0)
                    <form action="{{ route('admin.chatbot-logs.clear') }}" method="POST" onsubmit="return confirm('Permanently delete ALL chatbot logs?')">
                        @csrf @method('DELETE')
                        <input type="hidden" name="scope" value="all">
                        <button type="submit" class="px-3.5 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-bold">
                            Clear All
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <form action="{{ route('admin.chatbot-logs.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 pt-2 border-t border-slate-100">
            <input type="hidden" name="view" value="{{ $viewMode }}">
            @if($selectedSession !== '')
                <input type="hidden" name="session_id" value="{{ $selectedSession }}">
            @endif
            <div class="lg:col-span-5">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search questions, replies, session ID, or model..."
                       class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs outline-none focus:border-chaouen-500">
            </div>
            <div class="lg:col-span-3">
                <select name="status" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white text-xs">
                    <option value="">All Statuses</option>
                    <option value="answered" @selected(request('status') === 'answered')>Answered Only</option>
                    <option value="blocked" @selected(request('status') === 'blocked')>Guardrail Blocked Only</option>
                </select>
            </div>
            <div class="lg:col-span-2">
                <select name="provider" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white text-xs">
                    <option value="">All Providers</option>
                    @foreach($providers as $prov)
                        <option value="{{ $prov }}" @selected(request('provider') === $prov)>{{ $prov }}</option>
                    @endforeach
                </select>
            </div>
            <div class="lg:col-span-2 flex gap-2">
                <button type="submit" class="flex-1 px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold">Filter</button>
                <a href="{{ route('admin.chatbot-logs.index', ['view' => $viewMode]) }}" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold">Reset</a>
            </div>
        </form>
    </div>

    @if($selectedSession !== '')
        <!-- Session Thread View -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-4">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-chaouen-600">Session Conversation Thread</span>
                    <h2 class="font-bold text-sm text-slate-900 break-all">{{ $selectedSession }}</h2>
                </div>
                <form action="{{ route('admin.chatbot-logs.destroy-session') }}" method="POST" onsubmit="return confirm('Delete this entire chat session?')">
                    @csrf @method('DELETE')
                    <input type="hidden" name="session_id" value="{{ $selectedSession }}">
                    <button type="submit" class="px-4 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold">
                        Delete Entire Session
                    </button>
                </form>
            </div>

            @forelse($logs as $log)
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                    <div class="flex flex-wrap items-center justify-between gap-2 text-xs">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $log->was_blocked_off_topic ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700' }}">
                                {{ $log->was_blocked_off_topic ? 'Guardrail Blocked' : 'Answered' }}
                            </span>
                            <span class="font-semibold text-slate-600">{{ $log->provider_used ?: 'AI' }} · {{ $log->model_used }}</span>
                            <span class="text-slate-400">{{ $log->created_at?->format('Y-m-d H:i:s') }}</span>
                        </div>
                        <form action="{{ route('admin.chatbot-logs.destroy', $log) }}" method="POST" onsubmit="return confirm('Delete this chat message?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="px-2.5 py-1 rounded-lg bg-white border border-rose-200 text-rose-700 text-[11px] font-bold">Delete</button>
                        </form>
                    </div>
                    <div class="p-3 rounded-xl bg-chaouen-50 border border-chaouen-100 text-xs text-slate-900">
                        <strong class="block text-[10px] uppercase tracking-wider text-chaouen-700 mb-1">Visitor Question</strong>
                        <p class="whitespace-pre-line">{{ $log->user_message }}</p>
                    </div>
                    <div class="p-3 rounded-xl bg-white border border-slate-200 text-xs text-slate-700">
                        <strong class="block text-[10px] uppercase tracking-wider text-slate-500 mb-1">Chefchaouen Guide Reply</strong>
                        <p class="whitespace-pre-line leading-relaxed">{{ $log->bot_response }}</p>
                    </div>
                </div>
            @empty
                <p class="text-xs text-slate-500 py-4">No messages found for this session.</p>
            @endforelse
            <div>{{ $logs->links() }}</div>
        </div>

    @elseif($viewMode === 'sessions')
        <!-- Grouped Sessions Table -->
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase text-[10px] font-bold tracking-wider">
                        <tr>
                            <th class="px-4 py-3">Session</th>
                            <th class="px-4 py-3">Latest Question</th>
                            <th class="px-4 py-3">Messages</th>
                            <th class="px-4 py-3">Provider / Model</th>
                            <th class="px-4 py-3">Last Activity</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($sessions as $sess)
                            <tr class="hover:bg-slate-50/70">
                                <td class="px-4 py-3 font-mono text-[11px] text-slate-700 max-w-[160px] truncate">{{ $sess->session_id }}</td>
                                <td class="px-4 py-3 text-slate-900 font-medium max-w-xs truncate">{{ $sess->last_prompt }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 rounded-full bg-chaouen-50 text-chaouen-700 font-bold">{{ $sess->messages_count }}</span>
                                    @if($sess->blocked_count > 0)
                                        <span class="px-2 py-0.5 rounded-full bg-rose-50 text-rose-700 font-bold ml-1">{{ $sess->blocked_count }} blocked</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-slate-600">{{ $sess->provider_used }}<br><span class="text-[10px] text-slate-400">{{ $sess->model_used }}</span></td>
                                <td class="px-4 py-3 text-slate-500">{{ \Carbon\Carbon::parse($sess->last_activity_at)->diffForHumans() }}</td>
                                <td class="px-4 py-3 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="{{ route('admin.chatbot-logs.index', ['session_id' => $sess->session_id]) }}"
                                           class="px-3 py-1.5 rounded-lg bg-chaouen-600 hover:bg-chaouen-700 text-white font-bold">Open Thread</a>
                                        <form action="{{ route('admin.chatbot-logs.destroy-session') }}" method="POST" onsubmit="return confirm('Delete all messages in this session?')">
                                            @csrf @method('DELETE')
                                            <input type="hidden" name="session_id" value="{{ $sess->session_id }}">
                                            <input type="hidden" name="view" value="sessions">
                                            <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-rose-50 text-rose-700 font-bold">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-4 py-8 text-center text-slate-500">No chatbot sessions recorded yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-slate-100">{{ $sessions->links() }}</div>
        </div>

    @else
        <!-- Individual Chat Logs Table -->
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase text-[10px] font-bold tracking-wider">
                        <tr>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Visitor Question</th>
                            <th class="px-4 py-3">Bot Response</th>
                            <th class="px-4 py-3">Provider / Model</th>
                            <th class="px-4 py-3">Session &amp; Time</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($logs as $log)
                            <tr class="hover:bg-slate-50/70 align-top">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase {{ $log->was_blocked_off_topic ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700' }}">
                                        {{ $log->was_blocked_off_topic ? 'Blocked' : 'Answered' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 max-w-xs">
                                    <p class="font-semibold text-slate-900 line-clamp-2">{{ $log->user_message }}</p>
                                </td>
                                <td class="px-4 py-3 max-w-sm">
                                    <p class="text-slate-600 line-clamp-2">{{ strip_tags($log->bot_response) }}</p>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="font-semibold text-slate-800 block">{{ $log->provider_used ?: 'AI' }}</span>
                                    <span class="text-[10px] text-slate-400">{{ $log->model_used }}</span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <a href="{{ route('admin.chatbot-logs.index', ['session_id' => $log->session_id]) }}"
                                       class="font-mono text-[11px] text-chaouen-600 hover:underline block max-w-[130px] truncate" title="{{ $log->session_id }}">
                                        {{ $log->session_id ?: 'guest' }}
                                    </a>
                                    <span class="text-[10px] text-slate-400">{{ $log->created_at?->diffForHumans() }}</span>
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5">
                                        <button type="button"
                                                @click="activeLog = {{ Js::from([
                                                    'id' => $log->id,
                                                    'session_id' => $log->session_id,
                                                    'user_message' => $log->user_message,
                                                    'bot_response' => $log->bot_response,
                                                    'provider' => $log->provider_used,
                                                    'model' => $log->model_used,
                                                    'blocked' => $log->was_blocked_off_topic,
                                                    'created_at' => $log->created_at?->toDateTimeString(),
                                                ]) }}"
                                                class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-chaouen-50 text-slate-800 font-bold">
                                            View
                                        </button>
                                        <form action="{{ route('admin.chatbot-logs.destroy', $log) }}" method="POST" onsubmit="return confirm('Delete this chat log?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-4 py-8 text-center text-slate-500">No chatbot logs matched your filters.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-slate-100">{{ $logs->links() }}</div>
        </div>
    @endif

    <!-- Full Message Inspection Modal -->
    <div x-show="activeLog" x-cloak class="fixed inset-0 z-50 bg-slate-950/60 flex items-center justify-center p-4" @keydown.escape.window="activeLog = null">
        <div class="bg-white rounded-3xl border border-slate-200 max-w-2xl w-full max-h-[85vh] overflow-y-auto p-6 space-y-4 shadow-2xl" @click.away="activeLog = null">
            <div class="flex items-start justify-between gap-4 border-b border-slate-100 pb-3">
                <div>
                    <span class="text-[10px] font-bold uppercase px-2.5 py-0.5 rounded-full"
                          :class="activeLog?.blocked ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700'"
                          x-text="activeLog?.blocked ? 'Guardrail Blocked' : 'Answered'"></span>
                    <p class="text-xs text-slate-500 mt-1.5" x-text="(activeLog?.provider || 'AI') + ' · ' + (activeLog?.model || '') + ' · ' + (activeLog?.created_at || '')"></p>
                </div>
                <button type="button" @click="activeLog = null" class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-600 text-xs font-bold">✕ Close</button>
            </div>

            <div class="p-4 rounded-2xl bg-chaouen-50 border border-chaouen-100 text-xs text-slate-900">
                <strong class="block text-[10px] uppercase tracking-wider text-chaouen-700 mb-1">Visitor Question</strong>
                <p class="whitespace-pre-line" x-text="activeLog?.user_message"></p>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs text-slate-800">
                <strong class="block text-[10px] uppercase tracking-wider text-slate-500 mb-1">Chefchaouen Guide Response</strong>
                <p class="whitespace-pre-line leading-relaxed" x-text="activeLog?.bot_response"></p>
            </div>

            <div class="flex items-center justify-between pt-2">
                <a :href="'{{ route('admin.chatbot-logs.index') }}?session_id=' + encodeURIComponent(activeLog?.session_id || '')"
                   class="text-xs font-bold text-chaouen-600 hover:underline">
                    View Full Session Conversation →
                </a>
                <button type="button" @click="activeLog = null" class="px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold">Done</button>
            </div>
        </div>
    </div>
</div>
@endsection
