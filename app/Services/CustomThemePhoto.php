<?php

namespace App\Services;

use App\Support\FestiveTheme;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

/**
 * Header photo for the Custom theme.
 *
 * Every upload is decoded and saved again as a fresh JPEG: this drops hidden
 * data (GPS location, camera details), turns phone photos the right way up,
 * shrinks very large pictures, and means only a real image is ever stored.
 * Files live on the private disk and are served by ThemeController::photo.
 */
class CustomThemePhoto
{
    private const DIRECTORY = 'themes';

    private const MAX_WIDTH = 2400;

    private const MAX_HEIGHT = 1600;

    public function store(UploadedFile $file): string
    {
        $image = @imagecreatefromstring((string) file_get_contents($file->getRealPath()));

        if ($image === false) {
            throw ValidationException::withMessages([
                'custom_photo' => 'That photo could not be opened. Please use a JPG, PNG or WEBP picture.',
            ]);
        }

        $image = $this->upright($image, $file->getRealPath());

        $width = imagesx($image);
        $height = imagesy($image);
        $scale = min(1, self::MAX_WIDTH / $width, self::MAX_HEIGHT / $height);
        $newWidth = max(1, (int) round($width * $scale));
        $newHeight = max(1, (int) round($height * $scale));

        // White behind transparent PNGs, as JPEG has no transparency.
        $canvas = imagecreatetruecolor($newWidth, $newHeight);
        imagefill($canvas, 0, 0, imagecolorallocate($canvas, 255, 255, 255));
        imagecopyresampled($canvas, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        imagedestroy($image);

        ob_start();
        imagejpeg($canvas, null, 82);
        $jpeg = ob_get_clean();
        imagedestroy($canvas);

        $name = 'custom-' . bin2hex(random_bytes(10)) . '.jpg';
        Storage::disk('local')->put(self::DIRECTORY . '/' . $name, $jpeg);

        return $name;
    }

    public function delete(?string $name): void
    {
        if ($name && $this->isValidName($name)) {
            Storage::disk('local')->delete(self::DIRECTORY . '/' . $name);
        }
    }

    /** Full path of a stored photo, or null if the name is not one of ours or it is missing. */
    public function path(string $name): ?string
    {
        $relative = self::DIRECTORY . '/' . $name;

        return $this->isValidName($name) && Storage::disk('local')->exists($relative)
            ? Storage::disk('local')->path($relative)
            : null;
    }

    private function isValidName(string $name): bool
    {
        return (bool) preg_match('/^' . FestiveTheme::PHOTO_PATTERN . '$/', $name);
    }

    /** Phone cameras save some photos sideways and record the turn in EXIF. */
    private function upright(\GdImage $image, string $path): \GdImage
    {
        if (! function_exists('exif_read_data')) {
            return $image;
        }

        $orientation = (int) (@exif_read_data($path)['Orientation'] ?? 1);

        $rotated = match ($orientation) {
            3 => imagerotate($image, 180, 0),
            6 => imagerotate($image, -90, 0),
            8 => imagerotate($image, 90, 0),
            default => null,
        };

        if ($rotated) {
            imagedestroy($image);

            return $rotated;
        }

        return $image;
    }
}
