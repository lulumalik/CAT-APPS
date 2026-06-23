<?php

namespace App\Support;

class HtmlSanitizer
{
    private const ALLOWED_TAGS = '<p><br><strong><b><em><i><u><s><ul><ol><li><h1><h2><h3><h4><h5><h6><blockquote><a><img><table><thead><tbody><tr><th><td><span><div><hr><sub><sup>';

    public static function clean(?string $html): string
    {
        if ($html === null || $html === '') {
            return '';
        }

        $clean = strip_tags($html, self::ALLOWED_TAGS);
        $clean = preg_replace('/\s(on\w+|style)\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $clean) ?? $clean;
        $clean = preg_replace('/href\s*=\s*["\']?\s*javascript:/i', 'href="#"', $clean) ?? $clean;
        $clean = preg_replace('/src\s*=\s*["\']?\s*javascript:/i', 'src="#"', $clean) ?? $clean;

        return $clean;
    }
}
