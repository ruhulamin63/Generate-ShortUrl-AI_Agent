<?php
// app/Agents/ChatAgent.php
namespace App\Neuron\Agent;

use App\Models\Conversation;
use App\Neuron\ChatMemory\DatabaseChatHistory;
use App\Neuron\Tools\Group\CreateGroupTool;
use App\Neuron\Tools\Group\ListAllgroupsByUserTool;
use App\Neuron\Tools\GroupFromUrl\AssignGroupFromUrlTool;
use App\Neuron\Tools\GroupFromUrl\ListGroupsWithUrlTool;
use App\Neuron\Tools\GroupFromUrl\UnAssignGroupFromUrlTool;
use App\Neuron\Tools\Url\DeleteUrlTool;
use App\Neuron\Tools\Url\ListAllUrlByUser;
use App\Neuron\Tools\Url\SearchUrlByShortenedUrlTool;
use App\Neuron\Tools\Url\ShortenUrlTool;
use App\Neuron\Tools\Url\UpdateUrlInfoTool;
use App\Neuron\Tools\User\ShowUserInfoTool as UserShowUserInfoTool;
use App\Services\Url\DeleteUrlByShortCodeService;
use App\Services\Url\ListAllUrlByUserService;
use App\Services\Url\ShortenUrlService;
use NeuronAI\Agent;
use NeuronAI\Providers\AIProviderInterface;
use NeuronAI\Providers\Gemini\Gemini;
use NeuronAI\SystemPrompt;
use NeuronAI\Tools\Tool;
use NeuronAI\Tools\ToolProperty;
use App\Services\Group\CreateUrlGroupService;
use App\Services\Group\UpdateGroupService;
use App\Services\UrlGroup\AssignGroupFromUrlService;
use App\Services\UrlGroup\ListGroupsWithUrlsService;
use App\Services\UrlGroup\UnassignGroupFromUrlService;
use NeuronAI\MCP\McpConnector;

class ShortoAgent extends Agent
{
    protected Conversation $conversation;

    public function __construct(Conversation $conversation)
    {
        $this->conversation = $conversation;
    }

    public function provider(): AIProviderInterface
    {
        return new Gemini(
            key: env('GEMINI_API_KEY'),
            model: 'gemini-1.5-flash',
        );
    }

    public function chatHistory(): DatabaseChatHistory
    {
        $databaseChatHistory = new DatabaseChatHistory($this->conversation);
      //  $databaseChatHistory->c;
        return $databaseChatHistory;
    }

    public function instructions(): string
    {
        return new SystemPrompt(
            background: [
                'Eres Shorto, a virtual assistant specialized in URL management for a link shortening application. You help users create, organize, edit, and retrieve URLs and URL groups. You can also perform web searches and generate shortcodes based on the content found.',
                'Your goal is to provide clear, helpful, and accurate assistance within the context of URL management. You should only answer questions related to shortening URLs, creating groups, displaying user information, editing links, organizing collections, and generating shortcodes from web content.',
                'You must refer to the user by their username: ' . $this->conversation->user->username . ' and not by their real name.',
            ],
            steps: [
                'Follow the Thought → Action → Observation (TAO) method:',
                '• Thought: Analyze the user’s intent and determine whether the task falls within your responsibilities (URL management). Think about which tool you need to solve it (internal API, web search, etc.).',
                '• Action: Execute the task using the appropriate tool.',
                '• Observation: Verify if the expected result was achieved. If necessary, make adjustments or ask the user if they want to proceed with another related step.',

            ],
            output: [
                'Always respond in plain text or markdown format, never in JSON or other structured formats.',
                'When performing actions like editing a group or URL, first present the user with a numbered list to choose from.',
                'After any action, confirm what was done and offer to continue with a related task (for example, edit another URL, change the group name, etc.).',
                'Never respond to questions outside your domain (such as history, programming, science, etc.). If the user asks something out of context, politely inform them that you can only assist with URL management functions.',
            ]
        );
    }

    public function tools(): array
    {
        return [
            Tool::make(
                'shorten_url',
                'Acorta una URL larga y devuelve la URL acortada.',
            )->addProperty(
                new ToolProperty(
                    'originalUrl',
                    'string',
                    'URL original que deseas acortar.',
                    true
                )
            )->addProperty(
                new ToolProperty(
                    'customAlias',
                    'string',
                    'Alias personalizado para la URL. (opcional)',
                    false
                )
            )->addProperty(
                new ToolProperty(
                    'description',
                    'string',
                    'Descripción de la URL. (opcional)',
                    false
                )
            )->addProperty(
                new ToolProperty(
                    'password',
                    'string',
                    'Contraseña para acceder a la URL. (opcional)',
                    false
                )
            
            )->addProperty(
                new ToolProperty(
                    'shortenedUrl',
                    'string',
                    'URL acortada. (opcional)',
                    false
                )
            )
            ->setCallable(
                new ShortenUrlTool(app(ShortenUrlService::class))
            ),
            Tool::make(
                'sear_ch_url',
                'Busca una URL acortada en la base de datos y devuelve la URL original.',
            )->addProperty(
                new ToolProperty(
                    'shortened_url',
                    'string',
                    'La URL acortada que deseas buscar.',
                    true
                )
            )->setCallable(
                new SearchUrlByShortenedUrlTool()
            ),
            Tool::make(
                'update_url',
                'Actualiza la URL acortada en la base de datos. Los campos opcionales que no se envien no se actualizaran. Si no se envia el nuevo shortcode, se mantendra el mismo.',
            )->addProperty(
                    new ToolProperty(
                        'shortenedUrl',
                        'string',
                        'La URL acortada que deseas actualizar.',
                        true
                    )
                )
                ->addProperty(
                    new ToolProperty(
                        'customAlias',
                        'string',
                        'Alias personalizado para la URL. (opcional)',
                        false
                    )
                )->addProperty(
                    new ToolProperty(
                        'description',
                        'string',
                        'Descripción de la URL. (opcional)',
                        false
                    )
                )->addProperty(
                    new ToolProperty(
                        'password',
                        'string',
                        'Contraseña para acceder a la URL. (opcional)',
                        false
                    )
                )->addProperty(
                    new ToolProperty(
                        'newShortenedUrl',
                        'string',
                        'Nueva URL acortada. (opcional)',
                        false
                    )
                )->addProperty(
                    new ToolProperty(
                        'originalUrl',
                        'string',
                        'Nueva URL original. (opcional)',
                        false
                    )
                )->addProperty(
                    new ToolProperty(
                        'groupId',
                        'string',
                        'ID del grupo al que deseas agregar la URL. (opcional)',
                        false
                    )
                )->addProperty(
                    new ToolProperty(
                        'isActive',
                        'boolean',
                        'Estado de la URL (activa/inactiva). (opcional)',
                        false
                    )
                )->setCallable(
                    new UpdateUrlInfoTool()
                ),

            Tool::make(
                'delete_url',
                'Elimina una URL acortada de la base de datos.',
            )->addProperty(
                new ToolProperty(
                    'shortenedUrl',
                    'string',
                    'La URL acortada que deseas eliminar.',
                    true
                )
            )->setCallable(
                new DeleteUrlTool(app(DeleteUrlByShortCodeService::class))
            ),


            Tool::make(
                'list_all_urls_by_user',
                'Lista todas las URL acortadas por el usuario logeado.',
            )->setCallable(
                new ListAllUrlByUser(app(ListAllUrlByUserService::class))
            ),


            Tool::make(
                'create_group',
                'Crea un grupo de URLs.',
            )->addProperty(
                new ToolProperty(
                    'groupName',
                    'string',
                    'Nombre del grupo.',
                    true
                )
            )->addProperty(
                new ToolProperty(
                    'description',
                    'string',
                    'Descripción del grupo. (opcional)',
                    false
                )
            )->setCallable(
                new CreateGroupTool(app(CreateUrlGroupService::class))
            ),

            Tool::make(
                'edit_group',
                'Edita un grupo de URLs.',
            )->addProperty(
                new ToolProperty(
                    'groupId',
                    'string',
                    'ID del grupo.',
                    true
                )
                )->addProperty(
                    new ToolProperty(
                        'groupName',
                        'string',
                        'Nombre del grupo. (opcional)',
                        false
                    )
                )->addProperty(
                    new ToolProperty(
                        'description',
                        'string',
                        'Descripción del grupo. (opcional)',
                        false
                    )
                )->setCallable(
                    new \App\Neuron\Tools\Group\EditGroupTool(app(UpdateGroupService::class))
                ),


            Tool::make(
                'list_all_groups',
                'Lista todos los grupos de URLs del usuario logeado.',
            )->setCallable(
                new ListAllgroupsByUserTool()
            ),
            //Grupos de URLs
            Tool::make(
                'list_all_groups_urls',
                'Lista todos los grupos de URLs del usuario logeado y las URLs que contiene cada grupo.',
            )->setCallable(
                new ListGroupsWithUrlTool(app(ListGroupsWithUrlsService::class)
            )
            ) 
             ,Tool::make(
                'add_url_to_group',
                'Agrega una URL a un grupo de URLs.',
                    )->addProperty(
                new ToolProperty(
                    'groupId',
                    'string',
                    'ID del grupo al que deseas agregar la URL.',
                    true
                )
                    )->addProperty(
                new ToolProperty(
                    'shortenedUrl',
                    'string',
                    'La URL acortada que deseas agregar al grupo.',
                    true
                )
                    )->setCallable(
                new AssignGroupFromUrlTool(app(AssignGroupFromUrlService::class))
                    ),

                    Tool::make(
                'delete_url_from_group',
                'Elimina una URL de un grupo de URLs.',
                    )->addProperty(
                new ToolProperty(
                    'groupId',
                    'string',
                    'ID del grupo al que deseas agregar la URL.',
                    true
                )
                    )->addProperty(
                new ToolProperty(
                    'shortenedUrl',
                    'string',
                    'La URL acortada que deseas agregar al grupo.',
                    true
                )
                    )->setCallable(
                new UnAssignGroupFromUrlTool(app(UnassignGroupFromUrlService::class))
                    ),
            


            // Tool::make(
            //     'show_user_info',
            //     'Muestra información sobre el usuario logeado.',
            // )
            //     ->setCallable(
            //         new UserShowUserInfoTool()
            //     ),
            //      ...McpConnector::make([
            //     'command' => 'npx',
            //     'args' => ['-y', '@modelcontextprotocol/server-brave-search'],
            //     'env'=> ['BRAVE_API_KEY' => env('BRAVE_API_KEY')],
            // ])->tools(),
        ];
    }
}
