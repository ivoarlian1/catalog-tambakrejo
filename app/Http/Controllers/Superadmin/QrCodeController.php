<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Catalog;
use App\Support\CatalogQrCode;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class QrCodeController extends Controller
{
    public function show(Catalog $catalog, CatalogQrCode $qrCode): View
    {
        $this->authorize('view', $catalog);

        return view('superadmin.qrcode.show', [
            'catalog' => $catalog,
            'qrSvg' => $qrCode->svg($catalog, 320),
            'url' => $catalog->detail_url,
        ]);
    }

    public function download(Catalog $catalog, CatalogQrCode $qrCode): StreamedResponse
    {
        $this->authorize('view', $catalog);

        $svg = $qrCode->svg($catalog, 500);

        return response()->streamDownload(
            function () use ($svg): void {
                echo $svg;
            },
            'qr-'.$catalog->slug.'.svg',
            ['Content-Type' => 'image/svg+xml'],
        );
    }

    public function downloadJpeg(Catalog $catalog, CatalogQrCode $qrCode): StreamedResponse
    {
        $this->authorize('view', $catalog);

        $jpeg = $qrCode->jpeg($catalog, 1000);

        return response()->streamDownload(
            function () use ($jpeg): void {
                echo $jpeg;
            },
            'qr-'.$catalog->slug.'.jpg',
            ['Content-Type' => 'image/jpeg'],
        );
    }
}
