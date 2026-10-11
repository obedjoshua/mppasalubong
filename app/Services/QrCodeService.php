<?php

namespace App\Services;

use Exception;

class QrCodeService
{
    /**
     * Generate QR Code as Inline SVG string or image URL fallback.
     */
    public function generateTicketQr(string $ticketId, string $qrToken): array
    {
        $scanUrl = route('tickets.verify', ['orderTicket' => $ticketId, 'token' => $qrToken]);

        // PLAN A: Local PHP Library Generation (Fully Safe Dynamic Instantiation)
        $qrCodeClass = 'chillerlan\QRCode\QRCode';
        $qrOptionsClass = 'chillerlan\QRCode\QROptions';

        if (class_exists($qrCodeClass) && class_exists($qrOptionsClass)) {
            try {
                $options = new $qrOptionsClass([
                    'outputType' => $qrCodeClass::OUTPUT_MARKUP_SVG,
                    'eccLevel'   => $qrCodeClass::ECC_L,
                    'scale'      => 5,
                    'svgDefs'    => '<style>.qr-svg{fill:#1f2937;}</style>',
                ]);

                $svgData = (new $qrCodeClass($options))->render($scanUrl);

                return [
                    'mode' => 'inline_svg',
                    'content' => $svgData,
                ];
            } catch (Exception $e) {
                logger()->warning('Local QR Generation Failed: ' . $e->getMessage());
            }
        }

        // PLAN B: Public Cloud API Fallback
        $primaryApiUrl = "https://quickchart.io/qr?text=" . urlencode($scanUrl) . "&size=200&margin=1";
        $secondaryApiUrl = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($scanUrl);

        return [
            'mode' => 'external_image',
            'primary_url' => $primaryApiUrl,
            'fallback_url' => $secondaryApiUrl,
        ];
    }
}