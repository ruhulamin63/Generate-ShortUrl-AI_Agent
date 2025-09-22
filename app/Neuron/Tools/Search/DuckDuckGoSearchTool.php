<?php

namespace App\Neuron\Tools\Search;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class DuckDuckGoSearchTool
{
    private Client $httpClient;
    private string $baseUrl = 'https://api.duckduckgo.com/';

    public function __construct()
    {
        $this->httpClient = new Client([
            'timeout' => 30,
            'headers' => [
                'User-Agent' => 'Shorto URL Shortener Bot/1.0',
                'Accept' => 'application/json',
            ]
        ]);
    }

    /**
     * Execute DuckDuckGo search
     *
     * @param array $parameters
     * @return array
     */
    public function __invoke(array $parameters): array
    {
        try {
            $query = $parameters['query'] ?? '';
            
            if (empty($query)) {
                return [
                    'success' => false,
                    'error' => 'Search query is required'
                ];
            }

            // Build query parameters
            $queryParams = [
                'q' => $query,
                'format' => $parameters['format'] ?? 'json',
                'no_redirect' => $parameters['no_redirect'] ?? 1,
                'no_html' => $parameters['no_html'] ?? 1,
                'skip_disambig' => $parameters['skip_disambig'] ?? 1,
            ];

            // Make the API request
            $response = $this->httpClient->get($this->baseUrl, [
                'query' => $queryParams
            ]);

            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();

            if ($statusCode === 200) {
                $data = json_decode($body, true);
                
                if (json_last_error() === JSON_ERROR_NONE) {
                    return $this->formatSearchResults($data, $query);
                } else {
                    return [
                        'success' => false,
                        'error' => 'Failed to parse JSON response: ' . json_last_error_msg()
                    ];
                }
            } else {
                return [
                    'success' => false,
                    'error' => "API request failed with status code: {$statusCode}"
                ];
            }

        } catch (RequestException $e) {
            Log::error('DuckDuckGo API request failed', [
                'error' => $e->getMessage(),
                'query' => $parameters['query'] ?? 'Unknown'
            ]);

            return [
                'success' => false,
                'error' => 'Network error: ' . $e->getMessage()
            ];
        } catch (\Exception $e) {
            Log::error('DuckDuckGo search tool error', [
                'error' => $e->getMessage(),
                'query' => $parameters['query'] ?? 'Unknown'
            ]);

            return [
                'success' => false,
                'error' => 'Unexpected error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Format and filter search results for URL shortening context
     *
     * @param array $data
     * @param string $query
     * @return array
     */
    private function formatSearchResults(array $data, string $query): array
    {
        $results = [
            'success' => true,
            'query' => $query,
            'results' => []
        ];

        // Process Abstract if available
        if (!empty($data['Abstract'])) {
            $results['abstract'] = [
                'text' => $data['Abstract'],
                'source' => $data['AbstractSource'] ?? '',
                'url' => $data['AbstractURL'] ?? ''
            ];
        }

        // Process Answer if available
        if (!empty($data['Answer'])) {
            $results['answer'] = [
                'text' => $data['Answer'],
                'type' => $data['AnswerType'] ?? ''
            ];
        }

        // Process Definition if available
        if (!empty($data['Definition'])) {
            $results['definition'] = [
                'text' => $data['Definition'],
                'source' => $data['DefinitionSource'] ?? '',
                'url' => $data['DefinitionURL'] ?? ''
            ];
        }

        // Process Related Topics
        if (!empty($data['RelatedTopics']) && is_array($data['RelatedTopics'])) {
            $relatedTopics = [];
            
            foreach ($data['RelatedTopics'] as $topic) {
                if (is_array($topic) && !empty($topic['Text'])) {
                    $relatedTopics[] = [
                        'text' => $topic['Text'],
                        'url' => $topic['FirstURL'] ?? ''
                    ];
                }
            }
            
            if (!empty($relatedTopics)) {
                $results['related_topics'] = array_slice($relatedTopics, 0, 5); // Limit to 5 results
            }
        }

        // Process Search Results
        if (!empty($data['Results']) && is_array($data['Results'])) {
            $searchResults = [];
            
            foreach ($data['Results'] as $result) {
                if (is_array($result) && !empty($result['Text'])) {
                    $searchResults[] = [
                        'text' => $result['Text'],
                        'url' => $result['FirstURL'] ?? ''
                    ];
                }
            }
            
            if (!empty($searchResults)) {
                $results['search_results'] = array_slice($searchResults, 0, 10); // Limit to 10 results
            }
        }

        // Add metadata
        $results['metadata'] = [
            'type' => $data['Type'] ?? '',
            'redirect' => $data['Redirect'] ?? '',
            'has_results' => !empty($results['abstract']) || !empty($results['answer']) || 
                           !empty($results['definition']) || !empty($results['related_topics']) || 
                           !empty($results['search_results'])
        ];

        return $results;
    }

    /**
     * Search specifically for URL shortening related content
     *
     * @param string $url
     * @return array
     */
    public function searchForUrlInfo(string $url): array
    {
        $domain = parse_url($url, PHP_URL_HOST);
        $searchQuery = $domain ? "site:{$domain} url shortener" : "url shortener";
        
        return $this->__invoke([
            'query' => $searchQuery,
            'format' => 'json',
            'no_redirect' => true,
            'no_html' => true,
            'skip_disambig' => true
        ]);
    }

    /**
     * Search for content to generate shortcode suggestions
     *
     * @param string $url
     * @return array
     */
    public function generateShortcodeFromContent(string $url): array
    {
        try {
            // First, try to get the page title or content info
            $domain = parse_url($url, PHP_URL_HOST);
            $searchQuery = "site:{$domain}";
            
            $results = $this->__invoke([
                'query' => $searchQuery,
                'format' => 'json',
                'no_redirect' => true,
                'no_html' => true,
                'skip_disambig' => true
            ]);

            if ($results['success'] && !empty($results['abstract']['text'])) {
                // Generate shortcode suggestions based on content
                $content = strtolower($results['abstract']['text']);
                $words = preg_split('/\s+/', $content);
                $keywords = array_filter($words, function($word) {
                    return strlen($word) > 3 && !in_array($word, ['the', 'and', 'for', 'are', 'but', 'not', 'you', 'all']);
                });

                $suggestions = [];
                foreach (array_slice($keywords, 0, 3) as $keyword) {
                    $suggestions[] = substr($keyword, 0, 6); // Max 6 chars
                }

                return [
                    'success' => true,
                    'suggestions' => $suggestions,
                    'content_summary' => $results['abstract']['text']
                ];
            }

            return [
                'success' => false,
                'error' => 'No content found for shortcode generation'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Failed to generate shortcode: ' . $e->getMessage()
            ];
        }
    }
}