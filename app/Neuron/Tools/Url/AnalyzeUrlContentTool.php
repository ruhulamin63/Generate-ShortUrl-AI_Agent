<?php

namespace App\Neuron\Tools\Url;

use App\Neuron\Tools\Search\DuckDuckGoSearchTool;

class AnalyzeUrlContentTool
{
    private DuckDuckGoSearchTool $searchTool;

    public function __construct()
    {
        $this->searchTool = new DuckDuckGoSearchTool();
    }

    /**
     * Analyze URL content and suggest optimizations
     *
     * @param array $parameters
     * @return array
     */
    public function __invoke(array $parameters): array
    {
        $originalUrl = $parameters['originalUrl'] ?? '';
        
        if (empty($originalUrl)) {
            return [
                'success' => false,
                'error' => 'Original URL is required'
            ];
        }

        if (!filter_var($originalUrl, FILTER_VALIDATE_URL)) {
            return [
                'success' => false,
                'error' => 'Invalid URL format'
            ];
        }

        try {
            // Search for information about the URL
            $searchResults = $this->searchTool->searchForUrlInfo($originalUrl);
            
            // Generate shortcode suggestions
            $shortcodeResults = $this->searchTool->generateShortcodeFromContent($originalUrl);
            
            $analysis = [
                'success' => true,
                'url' => $originalUrl,
                'domain' => parse_url($originalUrl, PHP_URL_HOST),
                'path' => parse_url($originalUrl, PHP_URL_PATH),
                'content_info' => $searchResults,
                'suggested_shortcodes' => $shortcodeResults,
                'recommendations' => $this->generateRecommendations($originalUrl, $searchResults)
            ];

            return $analysis;

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Analysis failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Generate recommendations based on URL analysis
     *
     * @param string $url
     * @param array $searchResults
     * @return array
     */
    private function generateRecommendations(string $url, array $searchResults): array
    {
        $recommendations = [];
        
        // Check URL length
        if (strlen($url) > 100) {
            $recommendations[] = [
                'type' => 'optimization',
                'message' => 'URL is quite long (' . strlen($url) . ' characters). Shortening will significantly improve usability.'
            ];
        }

        // Check for tracking parameters
        if (strpos($url, '?utm_') !== false || strpos($url, '&utm_') !== false) {
            $recommendations[] = [
                'type' => 'privacy',
                'message' => 'URL contains tracking parameters. Consider creating a clean version.'
            ];
        }

        // Check for HTTPS
        if (strpos($url, 'https://') !== 0) {
            $recommendations[] = [
                'type' => 'security',
                'message' => 'URL uses HTTP instead of HTTPS. Ensure the destination is secure.'
            ];
        }

        // Content-based recommendations
        if ($searchResults['success'] && !empty($searchResults['abstract']['text'])) {
            $recommendations[] = [
                'type' => 'content',
                'message' => 'Found relevant content: ' . substr($searchResults['abstract']['text'], 0, 100) . '...'
            ];
        }

        return $recommendations;
    }
}