<?php
namespace App\Services;

use App\Core\Database;

class FormService {
    public static function handleSubmission(int $pageId, array $data): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO form_submissions (page_id, data_json) VALUES (?, ?)");
        return $stmt->execute([$pageId, json_encode($data)]);
    }
}
