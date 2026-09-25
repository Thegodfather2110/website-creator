<?php
namespace App\Services;

use App\Core\Database;

class ProjectService {
    public static function createProject(int $userId, string $name): int {
        $db = Database::getConnection();
        $slug = strtolower(str_replace(' ', '-', $name)) . '-' . time();

        $stmt = $db->prepare("INSERT INTO projects (user_id, name, slug) VALUES (?, ?, ?)");
        $stmt->execute([$userId, $name, $slug]);
        $projectId = (int)$db->lastInsertId();

        // Create default page
        $stmtPage = $db->prepare("INSERT INTO pages (project_id, name, slug, document_json) VALUES (?, ?, ?, ?)");
        $stmtPage->execute([$projectId, 'Home', '/', json_encode(['pages' => [['id' => 'page_home', 'root' => []]]])]);

        return $projectId;
    }

    public static function isUserOwner(int $userId, int $projectId): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id FROM projects WHERE id = ? AND user_id = ?");
        $stmt->execute([$projectId, $userId]);
        return (bool)$stmt->fetch();
    }
}
