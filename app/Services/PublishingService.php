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
        $cssArray = ['desktop' => '', 'tablet' => '', 'mobile' => ''];
        self::compileStylesRecursive($document['pages'][0]['root'] ?? [], $cssArray);

        $styles = $cssArray['desktop'] . "\n";
        if ($cssArray['tablet']) $styles .= "@media (max-width: 768px) { \n" . $cssArray['tablet'] . "}\n";
        if ($cssArray['mobile']) $styles .= "@media (max-width: 375px) { \n" . $cssArray['mobile'] . "}\n";

        $content = self::compileNodes($document['pages'][0]['root'] ?? []);

        return "<!DOCTYPE html><html><head><style>{$styles} body { margin: 0; font-family: sans-serif; }</style></head><body>{$content}</body></html>";
    }

    private static function compileStylesRecursive(array $nodes, array &$cssArr): void {
        foreach ($nodes as $node) {
            $id = $node['id'] ?? '';
            $styles = $node['styles'] ?? [];

            foreach (['desktop', 'tablet', 'mobile'] as $bp) {
                if (!empty($styles[$bp])) {
                    $cssArr[$bp] .= "#{$id} { " . self::styleArrayToCss($styles[$bp]) . " }\n";
                }
            }

            if (!empty($node['children'])) {
                self::compileStylesRecursive($node['children'], $cssArr);
            }
        }
    }

    private static function compileNodes(array $nodes): string {
        $html = "";
        foreach ($nodes as $node) {
            $tag = $node['tagName'] ?? 'div';
            $id = $node['id'] ?? '';
            $content = $node['content'] ?? '';

            $html .= "<{$tag} id='{$id}'>{$content}";
            if (!empty($node['children'])) {
                $html .= self::compileNodes($node['children']);
            }
            $html .= "</{$tag}>";
        }
        return $html;
    }

    private static function styleArrayToCss(array $styles): string {
        $css = "";
        foreach ($styles as $prop => $value) {
            $cssProp = strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $prop));
            $css .= "{$cssProp}: {$value};";
        }
        return $css;
    }
}
