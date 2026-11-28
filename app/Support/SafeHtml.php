<?php

namespace App\Support;

use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

/** HTML written in the admin (lessons, articles), stripped of scripts and event handlers before it reaches a page. */
class SafeHtml
{
    public static function clean(?string $html): string
    {
        static $sanitizer;
        $sanitizer ??= new HtmlSanitizer((new HtmlSanitizerConfig)
            ->allowSafeElements()
            ->allowLinkSchemes(['https', 'http', 'mailto'])
            ->allowMediaSchemes(['https', 'http'])
            ->allowRelativeLinks()
            ->allowRelativeMedias());

        return $sanitizer->sanitize((string) $html);
    }
}
