<?php

/**
 * Escapes Markdown special characters in the given text.
 *
 * @param string $text The input text containing Markdown.
 * @return string The escaped text with Markdown characters escaped.
 */
function escapeMarkdownV2($text) {

    // List of characters to escape
    $charsToEscape = ['\\', '_', '*', '[', ']', '(', ')', '~', '`', '>', '#', '+', '-', '=', '|', '{', '}', '.', '!'];

    // Escape each character with a backslash
    $escapedText = strtr($text, array_combine($charsToEscape, array_map(function($char) {
        return '\\' . $char;
    }, $charsToEscape)));

    return $escapedText;
    
}