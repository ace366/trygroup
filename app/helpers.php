<?php

if (!function_exists('getYouTubeID')) {
    function getYouTubeID($url) {
        preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $url, $matches);
        return $matches[1] ?? null;
    }
}
if (!function_exists('linkify')) {
    function linkify($text)
    {
        $pattern = '/(https?:\/\/[^\s]+)/u';
        return preg_replace_callback($pattern, function ($matches) {
            $url = e($matches[0]);
            return "<a href=\"$url\" target=\"_blank\" class=\"text-blue-500 underline break-all\">$url</a>";
        }, e($text));
    }
}
