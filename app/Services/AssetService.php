<?php
namespace App\Services;

use App\Core\Database;

class AssetService {
    public static function uploadAsset(int $projectId, array $file): ?array {
        // Simple file upload logic
        $uploadDir = __DIR__ . '/../../storage/uploads/' . $projectId . '/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $filename = uniqid() . '_' . basename($file['name']);
        $targetPath = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            $db = Database::getConnection();
            $stmt = $db->prepare("INSERT INTO assets (project_id, path, mime_type) VALUES (?, ?, ?)");
            $stmt->execute([$projectId, $filename, $file['type']]);
            return ['id' => $db->lastInsertId(), 'path' => $filename];
        }

        return null;
    }

    public static function listAssets(int $projectId): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id, path, mime_type FROM assets WHERE project_id = ?");
        $stmt->execute([$projectId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
