<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatbotLog;
use Illuminate\Http\Request;

class ChatbotLogController extends Controller
{
    public function index(Request $request)
    {
        $viewMode = $request->query('view', 'messages');
        $selectedSession = trim((string) $request->query('session_id', ''));

        $baseQuery = ChatbotLog::query();
        $this->applyFilters($baseQuery, $request);

        $stats = [
            'total' => ChatbotLog::count(),
            'answered' => ChatbotLog::where('was_blocked_off_topic', false)->count(),
            'blocked' => ChatbotLog::where('was_blocked_off_topic', true)->count(),
            'sessions' => ChatbotLog::distinct('session_id')->count('session_id'),
            'today' => ChatbotLog::where('created_at', '>=', now()->startOfDay())->count(),
        ];

        $providers = ChatbotLog::whereNotNull('provider_used')
            ->distinct()
            ->orderBy('provider_used')
            ->pluck('provider_used');

        if ($selectedSession !== '') {
            $logs = (clone $baseQuery)->orderBy('id')->paginate(50)->withQueryString();
            $sessions = collect();
        } elseif ($viewMode === 'sessions') {
            $sessions = (clone $baseQuery)
                ->selectRaw('session_id, COUNT(*) as messages_count, SUM(CASE WHEN was_blocked_off_topic = 1 THEN 1 ELSE 0 END) as blocked_count, MIN(created_at) as started_at, MAX(created_at) as last_activity_at, MAX(provider_used) as provider_used, MAX(model_used) as model_used')
                ->groupBy('session_id')
                ->orderByDesc('last_activity_at')
                ->paginate(20)
                ->withQueryString();

            $sessionIds = $sessions->pluck('session_id')->all();
            $latestBySession = ChatbotLog::whereIn('session_id', $sessionIds)
                ->orderByDesc('id')
                ->get()
                ->groupBy('session_id');

            $sessions->getCollection()->transform(function ($row) use ($latestBySession) {
                $thread = $latestBySession->get($row->session_id, collect());
                $row->last_prompt = $thread->first()?->user_message;
                $row->first_prompt = $thread->last()?->user_message;
                return $row;
            });

            $logs = collect();
        } else {
            $logs = (clone $baseQuery)->latest('id')->paginate(25)->withQueryString();
            $sessions = collect();
        }

        return view('admin.chatbot-logs.index', compact(
            'stats',
            'providers',
            'logs',
            'sessions',
            'viewMode',
            'selectedSession'
        ));
    }

    public function exportCsv(Request $request)
    {
        $query = ChatbotLog::query();
        $this->applyFilters($query, $request);
        $rows = $query->latest('id')->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="chefchaouen-chatbot-logs-' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['ID', 'Session ID', 'User Message', 'Bot Response', 'Provider', 'Model', 'Guardrail Status', 'Created At']);
            foreach ($rows as $row) {
                fputcsv($out, [
                    $row->id,
                    $row->session_id,
                    $row->user_message,
                    $row->bot_response,
                    $row->provider_used,
                    $row->model_used,
                    $row->was_blocked_off_topic ? 'blocked' : 'answered',
                    $row->created_at?->toDateTimeString(),
                ]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function destroy(ChatbotLog $chatbotLog)
    {
        $chatbotLog->delete();

        return back()->with('success', 'Chatbot log entry deleted.');
    }

    public function destroySession(Request $request)
    {
        $data = $request->validate([
            'session_id' => 'required|string|max:160',
        ]);

        $deleted = ChatbotLog::where('session_id', $data['session_id'])->delete();

        return redirect()
            ->route('admin.chatbot-logs.index', ['view' => $request->input('view', 'sessions')])
            ->with('success', "Deleted {$deleted} message(s) from chat session.");
    }

    public function clear(Request $request)
    {
        $scope = $request->input('scope', 'blocked');

        if ($scope === 'all') {
            $count = ChatbotLog::count();
            ChatbotLog::query()->delete();
            return redirect()->route('admin.chatbot-logs.index')->with('success', "Cleared all {$count} chatbot log(s).");
        }

        $count = ChatbotLog::where('was_blocked_off_topic', true)->delete();
        return redirect()->route('admin.chatbot-logs.index')->with('success', "Cleared {$count} guardrail-blocked log(s).");
    }

    protected function applyFilters($query, Request $request): void
    {
        if ($request->filled('q')) {
            $term = '%' . trim((string) $request->q) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('user_message', 'like', $term)
                    ->orWhere('bot_response', 'like', $term)
                    ->orWhere('session_id', 'like', $term)
                    ->orWhere('provider_used', 'like', $term)
                    ->orWhere('model_used', 'like', $term);
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'blocked') {
                $query->where('was_blocked_off_topic', true);
            } elseif ($request->status === 'answered') {
                $query->where('was_blocked_off_topic', false);
            }
        }

        if ($request->filled('provider')) {
            $query->where('provider_used', $request->provider);
        }

        if ($request->filled('session_id')) {
            $query->where('session_id', $request->session_id);
        }
    }
}
