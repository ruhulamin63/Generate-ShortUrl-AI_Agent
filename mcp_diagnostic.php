<?php

require_once __DIR__ . '/vendor/autoload.php';

use NeuronAI\MCP\McpConnector;

class McpServerDiagnostic
{
    public function testAllServers()
    {
        echo "=== MCP Server Diagnostic Tool ===\n\n";
        
        $servers = [
            'filesystem' => [
                'command' => 'npx',
                'args' => ['-y', '@modelcontextprotocol/server-filesystem'],
                'env' => ['MCP_FILESYSTEM_ALLOWED_DIRECTORIES' => getcwd()]
            ],
            'memory' => [
                'command' => 'npx',
                'args' => ['-y', '@modelcontextprotocol/server-memory']
            ],
            'sequential-thinking' => [
                'command' => 'npx',
                'args' => ['-y', '@modelcontextprotocol/server-sequential-thinking']
            ],
            'browser' => [
                'command' => 'npx',
                'args' => ['-y', '@agent-infra/mcp-server-browser']
            ],
            // The problematic one that doesn't exist
            'duckduckgo' => [
                'command' => 'npx',
                'args' => ['-y', '@modelcontextprotocol/server-duckduckgo']
            ]
        ];
        
        $results = [];
        
        foreach ($servers as $name => $config) {
            echo "Testing {$name} server...\n";
            $result = $this->testServer($name, $config);
            $results[$name] = $result;
            
            if ($result['success']) {
                echo "✅ {$name}: SUCCESS - {$result['tools_count']} tools available\n";
            } else {
                echo "❌ {$name}: FAILED - {$result['error']}\n";
            }
            echo "\n";
        }
        
        echo "=== Summary ===\n";
        $successful = array_filter($results, fn($r) => $r['success']);
        $failed = array_filter($results, fn($r) => !$r['success']);
        
        echo "Successful servers: " . count($successful) . "\n";
        echo "Failed servers: " . count($failed) . "\n\n";
        
        if (!empty($successful)) {
            echo "Working servers:\n";
            foreach ($successful as $name => $result) {
                echo "- {$name} ({$result['tools_count']} tools)\n";
            }
            echo "\n";
        }
        
        if (!empty($failed)) {
            echo "Failed servers (use fallback tools instead):\n";
            foreach ($failed as $name => $result) {
                echo "- {$name}: {$result['error']}\n";
            }
        }
        
        return $results;
    }
    
    private function testServer(string $name, array $config): array
    {
        try {
            $connector = McpConnector::make($config);
            $tools = $connector->tools();
            
            return [
                'success' => true,
                'tools_count' => count($tools),
                'tools' => array_map(fn($tool) => get_class($tool), $tools)
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'tools_count' => 0
            ];
        }
    }
    
    public function testDuckDuckGoAlternative()
    {
        echo "\n=== Testing DuckDuckGo Alternative ===\n";
        
        try {
            require_once __DIR__ . '/app/Neuron/Tools/Search/DuckDuckGoSearchTool.php';
            
            $tool = new \App\Neuron\Tools\Search\DuckDuckGoSearchTool();
            $result = $tool(['query' => 'laravel url shortener test']);
            
            if ($result['success']) {
                echo "✅ Custom DuckDuckGo tool: SUCCESS\n";
                echo "Query executed successfully\n";
                if (!empty($result['abstract'])) {
                    echo "Abstract found: " . substr($result['abstract']['text'], 0, 100) . "...\n";
                }
            } else {
                echo "❌ Custom DuckDuckGo tool: FAILED - {$result['error']}\n";
            }
        } catch (\Exception $e) {
            echo "❌ Custom DuckDuckGo tool: FAILED - {$e->getMessage()}\n";
        }
    }
}

// Run the diagnostic
$diagnostic = new McpServerDiagnostic();
$results = $diagnostic->testAllServers();
$diagnostic->testDuckDuckGoAlternative();

echo "\n=== Recommendations ===\n";
echo "1. Remove the non-existent '@modelcontextprotocol/server-duckduckgo' from your configuration\n";
echo "2. Use the custom DuckDuckGoSearchTool instead\n";
echo "3. Enable only working MCP servers in your .env configuration\n";
echo "4. Set MCP_FALLBACK_ON_ERROR=true to prevent complete failures\n";