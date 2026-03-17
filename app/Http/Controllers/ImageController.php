<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Arr;
use Intervention\Image\Encoders\PngEncoder;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\Encoders\WebpEncoder;

class ImageController extends Controller
{
    public function show(Request $request, $options, $path)
    {
        $path = public_path($path);
        abort_unless(File::exists($path), 404);

        $options = $this->parseOptions($options);

        $image = Image::read($path);

        if (Arr::hasAny($options, ["width", "height"])) {
            $width = $options["width"] ?? null;
            $height = $options["height"] ?? null;

            $image->scaleDown(width: $width, height: $height);
        }

        $format = Arr::get($options, "format", File::extension($path));
        $quality = (int) Arr::get($options, "quality", 100);

        $encoder = match ($format) {
            "png" => new PngEncoder(),
            "webp" => new WebpEncoder(quality: $quality),
            default => new JpegEncoder(quality: $quality),
        };

        $encoded = $image->encode($encoder);

        return response($encoded, 200)->header(
            "Content-Type",
            $encoded->mimetype(),
        );
    }

    protected function parseOptions($options)
    {
        return collect(explode(",", $options))
            ->mapWithKeys(function ($option) {
                $parsed = explode("=", $option);

                return [$parsed[0] => $parsed[1]];
            })
            ->toArray();
    }
}
