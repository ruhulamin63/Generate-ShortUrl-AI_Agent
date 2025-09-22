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
                'You are Shorto, a virtual assistant specialized in URL management for a link shortening application. You help users create, organize, edit, and consult URLs and groups of URLs. You can also perform web searches and, based on the content found, generate shortcodes to facilitate access.',
                'Your goal is to provide clear, useful, and precise assistance within the context of URL management. You should only respond to questions related to shortening URLs, creating groups, displaying user information, editing links, organizing collections, and generating shortcodes from web content.',
                'You must address the user by their username: ' . $this->conversation->user->username . ' and not by their real name.',
            ],
            steps: [
                'Follow the Thought → Action → Observation (TAO) method:',
                '• Thought: Analyze the user’s intent and determine whether the task falls within your functions (URL management). Think about which tool you need to solve it (internal API, web search, etc.).',
                '• Action: Execute the task using the appropriate tool.',
                '• Observation: Verify if the expected result was achieved. If necessary, make adjustments or ask the user if they need to proceed with another related step.'
            ],
            output: [
                'Always respond in plain text or markdown format, never in JSON or other structured formats.',
                'When it comes to actions such as editing a group or URL, first present the user with a numbered list so they can choose which item to act on.',
                'After any action, confirm what was done and offer to continue with a related task (for example, editing another URL, renaming the group, etc.).',
                'Never answer questions outside of your domain (such as history, programming, science, etc.). If the user asks for something out of context, kindly indicate that you can only help with functions related to URL management.'
            ]
        );
    }

    public function tools(): array
    {
        return [
            Tool::make(
                'shorten_url',
                'Shortens a long URL and returns the shortened URL.',
            )->addProperty(
                new ToolProperty(
                    'originalUrl',
                    'string',
                    'The original URL you want to shorten.',
                    true
                )
            )->addProperty(
                new ToolProperty(
                    'customAlias',
                    'string',
                    'Custom alias for the URL. (optional)',
                    false
                )
            )->addProperty(
                new ToolProperty(
                    'description',
                    'string',
                    'Description of the URL. (optional)',
                    false
                )
            )->addProperty(
                new ToolProperty(
                    'password',
                    'string',
                    'Password to access the URL. (optional)',
                    false
                )
            )->addProperty(
                new ToolProperty(
                    'shortenedUrl',
                    'string',
                    'Shortened URL. (optional)',
                    false
                )
            )
            ->setCallable(
                new ShortenUrlTool(app(ShortenUrlService::class))
            ),

            Tool::make(
                'sear_ch_url',
                'Searches for a shortened URL in the database and returns the original URL.',
            )->addProperty(
                new ToolProperty(
                    'shortened_url',
                    'string',
                    'The shortened URL you want to look up.',
                    true
                )
            )->setCallable(
                new SearchUrlByShortenedUrlTool()
            ),

            Tool::make(
                'update_url',
                'Updates the shortened URL in the database. Optional fields not sent will not be updated. If the new shortcode is not sent, the same one will be kept.',
            )->addProperty(
                    new ToolProperty(
                        'shortenedUrl',
                        'string',
                        'The shortened URL you want to update.',
                        true
                    )
                )
                ->addProperty(
                    new ToolProperty(
                        'customAlias',
                        'string',
                        'Custom alias for the URL. (optional)',
                        false
                    )
                )->addProperty(
                    new ToolProperty(
                        'description',
                        'string',
                        'Description of the URL. (optional)',
                        false
                    )
                )->addProperty(
                    new ToolProperty(
                        'password',
                        'string',
                        'Password to access the URL. (optional)',
                        false
                    )
                )->addProperty(
                    new ToolProperty(
                        'newShortenedUrl',
                        'string',
                        'New shortened URL. (optional)',
                        false
                    )
                )->addProperty(
                    new ToolProperty(
                        'originalUrl',
                        'string',
                        'New original URL. (optional)',
                        false
                    )
                )->addProperty(
                    new ToolProperty(
                        'groupId',
                        'string',
                        'ID of the group you want to add the URL to. (optional)',
                        false
                    )
                )->addProperty(
                    new ToolProperty(
                        'isActive',
                        'boolean',
                        'URL status (active/inactive). (optional)',
                        false
                    )
                )->setCallable(
                    new UpdateUrlInfoTool()
                ),

            Tool::make(
                'delete_url',
                'Deletes a shortened URL from the database.',
            )->addProperty(
                new ToolProperty(
                    'shortenedUrl',
                    'string',
                    'The shortened URL you want to delete.',
                    true
                )
            )->setCallable(
                new DeleteUrlTool(app(DeleteUrlByShortCodeService::class))
            ),


            Tool::make(
                'list_all_urls_by_user',
                'Lists all shortened URLs by the logged-in user.',
            )->setCallable(
                new ListAllUrlByUser(app(ListAllUrlByUserService::class))
            ),


            Tool::make(
                'create_group',
                'Creates a group of URLs.',
            )->addProperty(
                new ToolProperty(
                    'groupName',
                    'string',
                    'Group name.',
                    true
                )
            )->addProperty(
                new ToolProperty(
                    'description',
                    'string',
                    'Group description. (optional)',
                    false
                )
            )->setCallable(
                new CreateGroupTool(app(CreateUrlGroupService::class))
            ),

            Tool::make(
                'edit_group',
                'Edits a group of URLs.',
            )->addProperty(
                new ToolProperty(
                    'groupId',
                    'string',
                    'Group ID.',
                    true
                )
                )->addProperty(
                    new ToolProperty(
                        'groupName',
                        'string',
                        'Group name. (optional)',
                        false
                    )
                )->addProperty(
                    new ToolProperty(
                        'description',
                        'string',
                        'Group description. (optional)',
                        false
                    )
                )->setCallable(
                    new \App\Neuron\Tools\Group\EditGroupTool(app(UpdateGroupService::class))
                ),


            Tool::make(
                'list_all_groups',
                'Lists all URL groups of the logged-in user.',
            )->setCallable(
                new ListAllgroupsByUserTool()
            ),

            // URL Groups
            Tool::make(
                'list_all_groups_urls',
                'Lists all URL groups of the logged-in user and the URLs contained in each group.',
            )->setCallable(
                new ListGroupsWithUrlTool(app(ListGroupsWithUrlsService::class)
            )
            ),

            Tool::make(
                'add_url_to_group',
                'Adds a URL to a URL group.',
                    )->addProperty(
                new ToolProperty(
                    'groupId',
                    'string',
                    'ID of the group you want to add the URL to.',
                    true
                )
                    )->addProperty(
                new ToolProperty(
                    'shortenedUrl',
                    'string',
                    'The shortened URL you want to add to the group.',
                    true
                )
                    )->setCallable(
                new AssignGroupFromUrlTool(app(AssignGroupFromUrlService::class))
                    ),

            Tool::make(
                'delete_url_from_group',
                'Removes a URL from a URL group.',
                    )->addProperty(
                new ToolProperty(
                    'groupId',
                    'string',
                    'ID of the group you want to remove the URL from.',
                    true
                )
                    )->addProperty(
                new ToolProperty(
                    'shortenedUrl',
                    'string',
                    'The shortened URL you want to remove from the group.',
                    true
                )
                    )->setCallable(
                new UnAssignGroupFromUrlTool(app(UnassignGroupFromUrlService::class))
                    ),



            Tool::make(
                'show_user_info',
                'Displays information about the logged-in user.',
            )
            ->setCallable(
                new UserShowUserInfoTool()
            ),
            // ...McpConnector::make([
            //     'command' => 'npx',
            //     'args' => ['-y', '@modelcontextprotocol/server-brave-search'],
            //     'env'=> ['BRAVE_API_KEY' => env('BRAVE_API_KEY')],
            // ])->tools(),
            ...McpConnector::make([
                'command' => 'npx',
                'args' => ['-y', '@modelcontextprotocol/server-duckduckgo'],
            ])->tools(),
        ];
    }
}
