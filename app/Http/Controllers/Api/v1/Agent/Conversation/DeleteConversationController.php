<?php

namespace App\Http\Controllers\Api\v1\Agent\Conversation;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use Illuminate\Http\Request;

class DeleteConversationController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        Conversation::where('id', $request->conversation_id)
            ->where('user_id', $request->user()->id)
            ->delete();
        return response()->json([
            'message' => 'Conversation deleted successfully.',
        ], 200);
    }
}
