<?php

namespace App\Support;

use App\Models\Catalog;
use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Encoder\Encoder;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use RuntimeException;

class CatalogQrCode
{
    public function svg(Catalog $catalog, int $size = 280): string
    {
        return QrCode::format('svg')
            ->size($size)
            ->errorCorrection('M')
            ->margin(1)
            ->generate($catalog->detail_url);
    }

    public function jpeg(Catalog $catalog, int $size = 1000): string
    {
        if (! function_exists('imagecreatetruecolor') || ! function_exists('imagejpeg')) {
            throw new RuntimeException('PHP GD is required to generate JPEG QR codes.');
        }

        $matrix = Encoder::encode($catalog->detail_url, ErrorCorrectionLevel::H())->getMatrix();
        $moduleCount = $matrix->getWidth();
        $quietZone = 4;
        $scale = max(8, intdiv($size, $moduleCount + ($quietZone * 2)));
        $imageSize = ($moduleCount + ($quietZone * 2)) * $scale;
        $image = imagecreatetruecolor($imageSize, $imageSize);
        $white = imagecolorallocate($image, 255, 255, 255);
        $black = imagecolorallocate($image, 0, 0, 0);
        imagefill($image, 0, 0, $white);

        for ($y = 0; $y < $moduleCount; $y++) {
            for ($x = 0; $x < $moduleCount; $x++) {
                if ($matrix->get($x, $y) !== 1) {
                    continue;
                }

                $left = ($x + $quietZone) * $scale;
                $top = ($y + $quietZone) * $scale;
                imagefilledrectangle($image, $left, $top, $left + $scale - 1, $top + $scale - 1, $black);
            }
        }

        ob_start();
        imagejpeg($image, null, 95);
        $jpeg = ob_get_clean();
        imagedestroy($image);

        if (! is_string($jpeg) || ! str_starts_with($jpeg, "\xFF\xD8")) {
            throw new RuntimeException('The QR code could not be encoded as JPEG.');
        }

        return $jpeg;
    }
}
