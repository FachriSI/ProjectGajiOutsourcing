<?php

namespace App\Services;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeService
{
    /**
     * Generate QR code SVG
     * 
     * @param string $data
     * @param int $size
     * @return string SVG content
     */
    public function generate($data, $size = 200)
    {
        return QrCode::size($size)
            ->margin(2)
            ->errorCorrection('H')
            ->generate($data);
    }

    /**
     * Generate QR code PNG (base64)
     * 
     * @param string $data
     * @param int $size
     * @return string Base64 encoded PNG
     */
    public function generatePng($data, $size = 200)
    {
        $png = QrCode::format('png')
            ->size($size)
            ->margin(2)
            ->errorCorrection('H')
            ->generate($data);

        return base64_encode($png);
    }

    /**
     * Generate QR code with logo (if needed)
     * 
     * @param string $data
     * @param string $logoPath
     * @param int $size
     * @return string SVG content
     */
    public function generateWithLogo($data, $logoPath, $size = 200)
    {
        return QrCode::size($size)
            ->margin(2)
            ->errorCorrection('H')
            ->merge($logoPath, 0.3, true)
            ->generate($data);
    }
}
