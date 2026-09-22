<?php
namespace App\Services;

use App\Core\Database;

class CollaborationService {
    public static function getMembers(int $projectId): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT user_id, role FROM project_members WHERE project_id = ?");
        $stmt->execute([$projectId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function addMember(int $projectId, int $userId, string $role): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO project_members (project_id, user_id, role) VALUES (?, ?, ?)");
        return $stmt->execute([$projectId, $userId, $role]);
    }
}
