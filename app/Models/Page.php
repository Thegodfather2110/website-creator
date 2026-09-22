<?php
namespace App\Models;

use App\Core\Database;

class Page {
    public static function listByProject(int $projectId) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id, name, slug FROM pages WHERE project_id = ? ORDER BY created_at ASC");
        $stmt->execute([$projectId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function getById(int $id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM pages WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public static function updateDocument(int $id, string $documentJson) {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE pages SET document_json = ? WHERE id = ?");
        return $stmt->execute([$documentJson, $id]);
    }
}
