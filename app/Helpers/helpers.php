<?php

use Illuminate\Support\Str;

if (!function_exists('highlightProductText')) {
    function highlightProductText($text)
    {
        if (empty($text)) {
            return $text;
        }

        $highlightedSubtitle = $text;

        // ✅ Custom highlight words
        $highlightWords = ['লাইভ ওয়েট', 'Live Weight'];
        foreach ($highlightWords as $word) {
            $pattern = '/\(\s*' . preg_quote($word, '/') . '\s*\)/iu';
            $highlightedSubtitle = preg_replace(
                $pattern,
                '<span class="highlight-text">$0</span><br>',
                $highlightedSubtitle
            );
        }

        // ✅ Dynamic pattern: number + weight/piece
        $dynamicPattern = '/\(\s*.*?\d+(\.\d+)?\s*(কেজি|kg|গ্রাম|gm|পিস|pieces)\s*\)/iu';
        $highlightedSubtitle = preg_replace(
            $dynamicPattern,
            '<span class="highlight-text">$0</span><br>',
            $highlightedSubtitle
        );

        // ✅ Curly braces { } text
        $curlyPattern = '/\{[^}]*\}/u';
        $highlightedSubtitle = preg_replace(
            $curlyPattern,
            '<span class="highlight-text">$0</span><br>',
            $highlightedSubtitle
        );

        return $highlightedSubtitle;
    }
}
