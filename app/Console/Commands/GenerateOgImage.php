<?php

namespace App\Console\Commands;

use App\Support\Site;
use Illuminate\Console\Command;

/**
 * Renders the social preview card (og:image) as a PNG.
 *
 * Social platforms do not render SVG previews, so the card is rasterised here
 * with GD and the DejaVu fonts that ship with dompdf — no system fonts needed,
 * which keeps the output identical on macOS and inside the Docker image.
 */
class GenerateOgImage extends Command
{
    protected $signature = 'site:og-image {--path= : Where to write the PNG (defaults to public/images/og-default.png)}';

    protected $description = 'Generate the default social sharing image from the current site settings';

    public function handle(Site $site): int
    {
        if (! extension_loaded('gd')) {
            $this->error('The GD extension is required to generate the social image.');

            return self::FAILURE;
        }

        $width = 1200;
        $height = 630;
        $path = $this->option('path') ?: public_path('images/og-default.png');

        $fontRegular = base_path('vendor/dompdf/dompdf/lib/fonts/DejaVuSans.ttf');
        $fontBold = base_path('vendor/dompdf/dompdf/lib/fonts/DejaVuSans-Bold.ttf');

        foreach ([$fontRegular, $fontBold] as $font) {
            if (! is_file($font)) {
                $this->error("Font not found: {$font}");

                return self::FAILURE;
            }
        }

        $image = imagecreatetruecolor($width, $height);
        imageantialias($image, true);

        $ink = imagecolorallocate($image, 8, 8, 11);
        $white = imagecolorallocate($image, 255, 255, 255);
        $accent = imagecolorallocate($image, 124, 196, 255);
        $muted = imagecolorallocate($image, 139, 147, 165);
        $mutedStrong = imagecolorallocate($image, 182, 189, 204);
        $grid = imagecolorallocatealpha($image, 255, 255, 255, 118);

        imagefilledrectangle($image, 0, 0, $width, $height, $ink);

        // Ambient grid, fading out towards the bottom of the card.
        for ($x = 0; $x <= $width; $x += 48) {
            imageline($image, $x, 0, $x, (int) ($height * 0.72), $grid);
        }
        for ($y = 0; $y <= (int) ($height * 0.72); $y += 48) {
            imageline($image, 0, $y, $width, $y, $grid);
        }

        // Accent glow behind the headline. Drawn per pixel: stacked translucent
        // ellipses leave a visible seam where each layer ends.
        $glowX = (int) ($width * 0.66);
        $glowY = -40;
        $glowRadiusX = 700;
        $glowRadiusY = 520;
        $glowHeight = (int) ($height * 0.8);

        for ($y = 0; $y < $glowHeight; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $distance = sqrt(
                    (($x - $glowX) / $glowRadiusX) ** 2 +
                    (($y - $glowY) / $glowRadiusY) ** 2
                );

                if ($distance >= 1) {
                    continue;
                }

                // Smooth falloff, then fade the whole glow out towards the bottom.
                $intensity = (1 - $distance) ** 2.2 * (1 - $y / $glowHeight) * 0.42;
                $existing = imagecolorsforindex($image, imagecolorat($image, $x, $y));

                imagesetpixel($image, $x, $y, imagecolorallocate(
                    $image,
                    (int) min(255, $existing['red'] + 47 * $intensity),
                    (int) min(255, $existing['green'] + 127 * $intensity),
                    (int) min(255, $existing['blue'] + 255 * $intensity),
                ));
            }
        }

        // Bottom accent bar, blended from blue to cyan.
        for ($x = 0; $x < $width; $x++) {
            $ratio = $x / $width;
            $bar = imagecolorallocate(
                $image,
                (int) (28 + (34 - 28) * $ratio),
                (int) (95 + (211 - 95) * $ratio),
                (int) (214 + (238 - 214) * $ratio),
            );
            imagefilledrectangle($image, $x, $height - 10, $x + 1, $height, $bar);
        }

        $lines = [
            [$fontBold, 24, 80, 200, $accent, strtoupper((string) $site->get('headline'))],
            [$fontBold, 66, 80, 300, $white, $site->name()],
            [$fontBold, 34, 80, 372, $accent, (string) $site->get('tagline')],
            [$fontRegular, 22, 80, 440, $mutedStrong, 'Portals · Dashboards · Salesforce, Marketo & HubSpot integrations'],
            [$fontRegular, 18, 80, 540, $muted, 'PHP · Laravel · Yii · Node.js · NestJS · Vue · React · MySQL · MongoDB · RabbitMQ'],
        ];

        foreach ($lines as [$font, $size, $x, $y, $color, $text]) {
            imagettftext($image, $size, 0, $x, $y, $color, $font, $text);
        }

        if (! is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        imagepng($image, $path, 6);
        imagedestroy($image);

        $this->info('Social image written to '.str_replace(base_path().'/', '', $path));
        $this->line('  '.number_format(filesize($path) / 1024, 1).' KB · '.$width.'×'.$height);

        return self::SUCCESS;
    }
}
