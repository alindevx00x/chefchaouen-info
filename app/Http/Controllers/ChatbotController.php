<?php

namespace App\Http\Controllers;

use App\Models\ChatbotLog;
use App\Services\AiService;
use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    public function ask(Request $request, AiService $aiService)
    {
        $data = $request->validate([
            'message' => 'required|string|min:2|max:1000',
            'history' => 'nullable|array|max:10',
        ]);

        $response = $aiService->chatWithChefchaouenGuide(
            $data['message'],
            $data['history'] ?? []
        );

        ChatbotLog::create([
            'session_id' => $request->session()->getId(),
            'user_message' => $data['message'],
            'bot_response' => $response['reply'],
            'provider_used' => $response['provider'] ?? null,
            'model_used' => $response['model'] ?? null,
            'was_blocked_off_topic' => (bool) ($response['blocked'] ?? false),
        ]);

        return response()->json([
            'success' => true,
            'reply' => $response['reply'],
            'blocked' => (bool) ($response['blocked'] ?? false),
            'provider' => $response['provider'] ?? 'Chefchaouen Guide',
            'model' => $response['model'] ?? 'grounded',
        ]);
    }
}
