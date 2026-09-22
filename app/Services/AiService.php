<?php
namespace App\Services;

class AiService {
    public static function generateStructure(string $prompt): array {
        // In a real scenario, this would call the Claude API
        // For now, return a placeholder JSON document model
        return [
            'tagName' => 'section',
            'id' => 'gen_' . uniqid(),
            'content' => 'AI Generated Section: ' . $prompt,
            'styles' => ['padding' => '20px', 'backgroundColor' => '#f0f0f0']
        ];
    }
}
