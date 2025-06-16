<?php

namespace App\Neuron\ChatMemory;

use App\Models\Conversation;
use App\Models\Message;
use NeuronAI\Chat\History\AbstractChatHistory;
use NeuronAI\Chat\Messages\Message as ChatMessage;
use NeuronAI\Chat\Messages\UserMessage;
use NeuronAI\Chat\Messages\AssistantMessage;

// Clase que hereda de AbstractChatHistory para almacenar el historial en la base de datos
class DatabaseChatHistory extends AbstractChatHistory
{
    // Objeto conversación que contiene el historial
    protected Conversation $conversation;

    public function __construct(Conversation $conversation, int $contextWindow = 90000)
    {
        $this->conversation = $conversation;
        $this->contextWindow = $contextWindow;
        // Carga el historial de mensajes desde la base de datos
        $this->loadMessages();
       // $this->removeOldestMessage();
    }


    // Método para agregar un mensaje al historial
    protected function storeMessage(ChatMessage $message): static
    {
        Message::create([
            'conversation_id' => $this->conversation->id,
            'sender' => $message->getRole(),
            'message' => $message->getContent()?? 'tool call',
            // 'user_id' => $this->conversation->user_id, // Descomenta si lo necesitas
        ]);
// return $this porque el método devuelve la instancia actual de la clase
        return $this;
    }

    public function removeOldestMessage(): static
    {
        $this->conversation->messages()->oldest()->first()?->delete();
        return $this;
    }

    protected function clear(): static
    {
        $this->conversation->messages()->delete();
        return $this;
    }

    protected function loadMessages(): void
    {
        // Carga todos los mensajes de la conversación desde la base de datos
        // y los ordena por fecha de creación ascendente
        // Se utiliza el método orderBy para ordenar los mensajes por fecha de creación
        // y el método get para obtener todos los mensajes
        // Se utiliza un bucle foreach para iterar sobre cada mensaje
        // y se utiliza el operador match para determinar el tipo de mensaje
        // basado en el campo 'sender' de la base de datos
        
        foreach ($this->conversation->messages()->orderBy('created_at')->get() as $msg) {
            // Determina el tipo de mensaje basado en el campo 'sender'
            $message = match ($msg->sender) {
                ChatMessage::ROLE_USER => new UserMessage($msg->message),
                ChatMessage::ROLE_ASSISTANT => new AssistantMessage($msg->message),
                default => new ChatMessage($msg->sender, $msg->message)
            };
            // $message = match ($msg->sender) {
            //     'user' => new UserMessage($msg->message), // Changed to string
            //     'assistant' => new AssistantMessage($msg->message), // Changed to string
            //     default => new ChatMessage($msg->sender, $msg->message)
            // };
            
            // Agrega el mensaje al historial
            $this->history[] = $message;
        }
    }



}
