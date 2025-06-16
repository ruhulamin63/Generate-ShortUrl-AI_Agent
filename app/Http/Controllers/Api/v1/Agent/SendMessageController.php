<?php

namespace App\Http\Controllers\Api\v1\Agent;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Neuron\Agent\ShortoAgent;
use Illuminate\Http\Request;
use Inspector\Configuration;
use NeuronAI\Chat\Messages\UserMessage;
use NeuronAI\Observability\AgentMonitoring;

class SendMessageController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'message' => 'required|string',
        ]);



        $conversation = Conversation::where('id', $request->conversation_id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        echo $conversation->user->username;

        $agent = new ShortoAgent($conversation);
        $userMessage = new UserMessage($request->message);
        $response = $agent->make($conversation)
            // ->observe(new AgentMonitoring(inspector()))
            ->withChatHistory($agent->chatHistory())
            ->chat($userMessage);

        return response()->json([
            'response' => $response,
        ]);
    }
}
