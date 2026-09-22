<?php
namespace App\Models;

use App\Core\Database;

class Page {
    public static function getById($id) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM pages WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public static function updateDocument($id, $documentJson) {
        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE pages SET document_json = ? WHERE id = ?");
        return $stmt->execute([$documentJson, $id]);
    }
}
