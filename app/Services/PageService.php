<?php
namespace App\Services;

use App\Core\Database;
use App\Models\Page;

class PageService {
    public static function getPagesForProject(int $projectId): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id, name, slug FROM pages WHERE project_id = ?");
        $stmt->execute([$projectId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function createPage(int $projectId, string $name, string $slug): bool {
        $db = Database::getConnection();
        // Initial empty document JSON structure
        $doc = json_encode(['pages' => [['id' => 'page_root', 'root' => []]]]);
        $stmt = $db->prepare("INSERT INTO pages (project_id, name, slug, document_json) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$projectId, $name, $slug, $doc]);
    }

    public static function deletePage(int $pageId): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM pages WHERE id = ?");
        return $stmt->execute([$pageId]);
    }
}
