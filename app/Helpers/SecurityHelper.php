<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class SecurityHelper
{
    /**
     * Sanitize user input to prevent XSS attacks
     *
     * @param string|null $input
     * @param array $allowedTags
     * @return string|null
     */
    public static function sanitizeInput(?string $input, array $allowedTags = []): ?string
    {
        if ($input === null) {
            return null;
        }

        // Trim whitespace
        $input = trim($input);

        // Remove null bytes
        $input = str_replace("\0", '', $input);

        // Remove control characters except newlines and tabs
        $input = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $input);

        // Strip HTML tags if no allowed tags specified
        if (empty($allowedTags)) {
            $input = strip_tags($input);
        } else {
            $input = strip_tags($input, $allowedTags);
        }

        // Convert special characters to HTML entities
        $input = htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return $input;
    }

    /**
     * Sanitize HTML content with allowed tags
     *
     * @param string|null $html
     * @return string|null
     */
    public static function sanitizeHtml(?string $html): ?string
    {
        if ($html === null) {
            return null;
        }

        // Define allowed tags for rich content
        $allowedTags = [
            'p', 'br', 'strong', 'b', 'em', 'i', 'u', 'ul', 'ol', 'li',
            'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'blockquote', 'code', 'pre'
        ];

        $allowedAttributes = ['class', 'id', 'style'];

        // Use DOMDocument for better HTML sanitization
        $dom = new \DOMDocument();

        // Suppress warnings for malformed HTML
        libxml_use_internal_errors(true);

        $dom->loadHTML('<div>' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        // Remove dangerous elements and attributes
        self::cleanDomNode($dom->documentElement, $allowedTags, $allowedAttributes);

        // Get clean HTML
        $cleanHtml = '';
        foreach ($dom->documentElement->childNodes as $node) {
            $cleanHtml .= $dom->saveHTML($node);
        }

        return $cleanHtml;
    }

    /**
     * Recursively clean DOM nodes
     */
    private static function cleanDomNode(\DOMNode $node, array $allowedTags, array $allowedAttributes): void
    {
        if ($node->nodeType === XML_ELEMENT_NODE) {
            // Remove disallowed tags
            if (!in_array(strtolower($node->tagName), $allowedTags)) {
                $node->parentNode->removeChild($node);
                return;
            }

            // Remove disallowed attributes
            $attributesToRemove = [];
            foreach ($node->attributes as $attr) {
                if (!in_array(strtolower($attr->name), $allowedAttributes)) {
                    $attributesToRemove[] = $attr->name;
                } else {
                    // Sanitize attribute values
                    $attr->value = self::sanitizeAttributeValue($attr->value);
                }
            }

            foreach ($attributesToRemove as $attrName) {
                $node->removeAttribute($attrName);
            }
        }

        // Process child nodes
        $childNodes = [];
        foreach ($node->childNodes as $child) {
            $childNodes[] = $child;
        }

        foreach ($childNodes as $child) {
            self::cleanDomNode($child, $allowedTags, $allowedAttributes);
        }
    }

    /**
     * Sanitize attribute values
     */
    private static function sanitizeAttributeValue(string $value): string
    {
        // Remove javascript: and data: protocols
        $value = preg_replace('/\b(javascript|data|vbscript):/i', '', $value);

        // Remove event handlers
        $value = preg_replace('/\bon\w+\s*=/i', '', $value);

        return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Validate and sanitize email addresses
     *
     * @param string|null $email
     * @return string|null
     */
    public static function sanitizeEmail(?string $email): ?string
    {
        if ($email === null) {
            return null;
        }

        $email = trim($email);
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);

        // Additional validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return null;
        }

        return $email;
    }

    /**
     * Sanitize URLs
     *
     * @param string|null $url
     * @return string|null
     */
    public static function sanitizeUrl(?string $url): ?string
    {
        if ($url === null) {
            return null;
        }

        $url = trim($url);

        // Add protocol if missing
        if (!preg_match('/^https?:\/\//i', $url)) {
            $url = 'https://' . $url;
        }

        // Validate URL
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        // Only allow http and https protocols
        $parsed = parse_url($url);
        if (!in_array($parsed['scheme'] ?? '', ['http', 'https'])) {
            return null;
        }

        return $url;
    }

    /**
     * Generate secure random string
     *
     * @param int $length
     * @return string
     */
    public static function generateSecureToken(int $length = 32): string
    {
        return bin2hex(random_bytes($length / 2));
    }

    /**
     * Hash sensitive data for logging
     *
     * @param string $data
     * @return string
     */
    public static function hashForLogging(string $data): string
    {
        return hash('sha256', $data . config('app.key'));
    }

    /**
     * Check if input contains suspicious patterns
     *
     * @param string $input
     * @return bool
     */
    public static function containsSuspiciousPatterns(string $input): bool
    {
        $suspiciousPatterns = [
            '/<script/i',
            '/javascript:/i',
            '/on\w+\s*=/i',
            '/vbscript:/i',
            '/data:text/i',
            '/expression\s*\(/i',
            '/eval\s*\(/i',
            '/document\.cookie/i',
            '/document\.location/i',
            '/window\.location/i',
        ];

        foreach ($suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Rate limit check for specific actions
     *
     * @param string $key
     * @param int $maxAttempts
     * @param int $decayMinutes
     * @return bool
     */
    public static function checkRateLimit(string $key, int $maxAttempts = 5, int $decayMinutes = 15): bool
    {
        $cacheKey = 'rate_limit_' . $key;
        $attempts = cache()->get($cacheKey, 0);

        if ($attempts >= $maxAttempts) {
            return false;
        }

        cache()->put($cacheKey, $attempts + 1, now()->addMinutes($decayMinutes));
        return true;
    }
}