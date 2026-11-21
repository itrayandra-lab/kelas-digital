<?php

namespace App\Support;

use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class TicketQr
{
    /** QR code of the ticket link as SVG markup; pure PHP, so it works on any server and inside the PDF. */
    public static function svg(string $text, int $size = 240): string
    {
        $svg = (new Writer(new ImageRenderer(new RendererStyle($size, 1), new SvgImageBackEnd)))->writeString($text);

        return preg_replace('/^<\?xml[^>]*>\s*/', '', $svg);
    }

    public static function dataUri(string $text, int $size = 240): string
    {
        return 'data:image/svg+xml;base64,'.base64_encode(self::svg($text, $size));
    }
}
