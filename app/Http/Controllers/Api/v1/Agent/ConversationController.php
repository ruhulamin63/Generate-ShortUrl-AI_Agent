<?php

namespace App\Http\Controllers\Api\v1\Agent;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $conversation = Conversation::create([
            'title' => $request->title,
            'user_id' => $request->user()->id,
        ]);

        return response()->json([
            'conversation' => $conversation,
        ]);
    }
}
