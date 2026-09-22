<?php
namespace App\Services;

use App\Core\Database;

class TemplateService {
    public static function listTemplates(): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id, name, description, thumbnail_path FROM templates");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function getTemplateDocument(int $templateId): ?string {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT document_json FROM templates WHERE id = ?");
        $stmt->execute([$templateId]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ? $row['document_json'] : null;
    }
}
