<?php
namespace App\Services;

use App\Core\Database;

class CommentService {
    public static function getComments(int $pageId): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM comments WHERE page_id = ? ORDER BY created_at DESC");
        $stmt->execute([$pageId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function addComment(int $pageId, int $userId, string $nodeId, string $content): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO comments (page_id, user_id, node_id, content) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$pageId, $userId, $nodeId, $content]);
    }
}
