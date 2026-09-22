<?php
namespace App\Services;

use App\Models\Page;

class PublishingService {
    public static function compile(int $pageId): string {
        $page = Page::getById($pageId);
        if (!$page) throw new \Exception("Page not found");

        $document = json_decode($page['document_json'], true);
        if (!$document) throw new \Exception("Invalid document JSON");

        // Compile styles and nodes
        $styles = self::compileStyles($document['pages'][0] ?? []);
        $content = self::compileNodes($document['pages'][0]['root'] ?? []);

        return "<!DOCTYPE html><html><head><style>{$styles}</style></head><body>{$content}</body></html>";
    }

    private static function compileStyles(array $page): string {
        // Basic style extraction from root node or global styles
        return "body { margin: 0; font-family: sans-serif; } .node { border: 1px solid transparent; }";
    }

    private static function compileNodes(array $nodes): string {
        $html = "";
        foreach ($nodes as $node) {
            $tag = $node['tagName'] ?? 'div';
            $id = $node['id'] ?? '';
            $content = $node['content'] ?? '';

            // Build style string
            $styleStr = "";
            if (!empty($node['styles'])) {
                $styles = is_array($node['styles']) ? $node['styles'] : [];
                foreach ($styles as $prop => $value) {
                    // Simple property to CSS mapping
                    $cssProp = strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $prop));
                    $styleStr .= "{$cssProp}: {$value};";
                }
            }

            $html .= "<{$tag} id='{$id}' style='{$styleStr}'>{$content}";
            // Recursive children
            if (!empty($node['children'])) {
                $html .= self::compileNodes($node['children']);
            }
            $html .= "</{$tag}>";
        }
        return $html;
    }
}
