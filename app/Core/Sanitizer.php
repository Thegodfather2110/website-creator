<?php
namespace App\Core;

class Sanitizer {
    public static function sanitizeHtml(string $html): string {
        // Simple sanitization: in production use a library like HTMLPurifier
        return strip_tags($html, '<p><a><div><span><h1><h2><h3><h4><h5><h6><ul><ol><li><img><br>');
    }
}
