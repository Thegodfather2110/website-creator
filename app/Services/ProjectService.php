<?php
namespace App\Services;

use App\Core\Database;

class ProjectService {
    public static function createDefaultProject(int $userId, string $name): array {
        $db = Database::getConnection();
        $slug = strtolower(str_replace(' ', '-', $name)) . '-' . time();

        // 1. Create Project
        $stmt = $db->prepare("INSERT INTO projects (user_id, name, slug) VALUES (?, ?, ?)");
        $stmt->execute([$userId, $name, $slug]);
        $projectId = (int)$db->lastInsertId();

        // 2. Create Default 'Home' Page
        $stmtPage = $db->prepare("INSERT INTO pages (project_id, name, slug, document_json) VALUES (?, ?, ?, ?)");
        $stmtPage->execute([$projectId, 'Home', '/', json_encode(['pages' => [['id' => 'page_home', 'root' => []]]])]);
        $pageId = (int)$db->lastInsertId();

        return [
            'project_id' => $projectId,
            'page_id' => $pageId
        ];
    }
}
