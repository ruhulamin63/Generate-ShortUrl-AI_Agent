<?php

return [
    /*
    |--------------------------------------------------------------------------
    | MCP Server Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Model Context Protocol (MCP) servers.
    | These servers provide additional tools and capabilities to the AI agent.
    |
    */

    'enabled' => env('MCP_ENABLED', true),
    
    'timeout' => env('MCP_TIMEOUT', 30),
    
    'retry_attempts' => env('MCP_RETRY_ATTEMPTS', 3),
    
    'fallback_on_error' => env('MCP_FALLBACK_ON_ERROR', true),
    
    /*
    |--------------------------------------------------------------------------
    | Available MCP Servers
    |--------------------------------------------------------------------------
    |
    | List of MCP servers that the agent can attempt to use.
    | Servers are tried in order, and failed servers are skipped.
    |
    */
    
    'servers' => [
        // Filesystem access (verified working)
        'filesystem' => [
            'enabled' => env('MCP_FILESYSTEM_ENABLED', true),
            'command' => 'npx',
            'args' => ['-y', '@modelcontextprotocol/server-filesystem'],
            'env' => [
                'MCP_FILESYSTEM_ALLOWED_DIRECTORIES' => env('MCP_FILESYSTEM_ALLOWED_DIRS', getcwd())
            ],
            'description' => 'Provides file system access capabilities'
        ],
        
        // Browser automation (may work depending on system)
        'browser' => [
            'enabled' => env('MCP_BROWSER_ENABLED', false), // Disabled by default
            'command' => 'npx',
            'args' => ['-y', '@agent-infra/mcp-server-browser'],
            'description' => 'Provides web browser automation capabilities'
        ],
        
        // Memory server (verified working)
        'memory' => [
            'enabled' => env('MCP_MEMORY_ENABLED', false),
            'command' => 'npx',
            'args' => ['-y', '@modelcontextprotocol/server-memory'],
            'description' => 'Provides persistent memory capabilities'
        ],
        
        // Sequential thinking (verified working)
        'sequential_thinking' => [
            'enabled' => env('MCP_SEQUENTIAL_THINKING_ENABLED', false),
            'command' => 'npx',
            'args' => ['-y', '@modelcontextprotocol/server-sequential-thinking'],
            'description' => 'Provides structured thinking capabilities'
        ]
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Fallback Configuration
    |--------------------------------------------------------------------------
    |
    | When MCP servers fail, these native tools will be used instead.
    |
    */
    
    'fallback_tools' => [
        'search' => \App\Neuron\Tools\Search\DuckDuckGoSearchTool::class,
        'url_analysis' => \App\Neuron\Tools\Url\AnalyzeUrlContentTool::class,
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Error Handling
    |--------------------------------------------------------------------------
    |
    | How to handle MCP server errors and failures.
    |
    */
    
    'error_handling' => [
        'log_errors' => env('MCP_LOG_ERRORS', true),
        'continue_on_error' => env('MCP_CONTINUE_ON_ERROR', true),
        'max_consecutive_failures' => env('MCP_MAX_CONSECUTIVE_FAILURES', 5),
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Debugging
    |--------------------------------------------------------------------------
    |
    | Debug configuration for MCP servers.
    |
    */
    
    'debug' => [
        'enabled' => env('MCP_DEBUG', false),
        'log_requests' => env('MCP_LOG_REQUESTS', false),
        'log_responses' => env('MCP_LOG_RESPONSES', false),
    ]
];