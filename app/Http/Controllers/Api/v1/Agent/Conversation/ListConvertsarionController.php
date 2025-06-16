<?php

namespace App\Http\Controllers\Api\v1\Agent\Conversation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ListConvertsarionController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $conversations = $request->user()->conversations()->get();
        if ($conversations->isEmpty()) {
            return response()->json([
                'message' => 'No conversations found.',
            ], 404);
        }
        return response()->json([
            'conversations' => $conversations,
        ], 200);
    }
}
