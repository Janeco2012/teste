<?php

namespace Weserv\Images\Manipulators;

use Jcupitt\Vips\BandFormat;
use Jcupitt\Vips\BlendMode;
use Jcupitt\Vips\Image;
use Weserv\Images\Manipulators\Helpers\Color;

/**
 * @property string $bg
 * @property bool $isPremultiplied
 */
class Background extends BaseManipulator
{
    /**
     * Perform background image manipulation.
     *
     * @param Image $image The source image.
     *
     * @throws \Jcupitt\Vips\Exception
     *
     * @return Image The manipulated image.
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function run(Image $image): Image
    {
        // Skip this manipulator if:
        // - There's no bg parameter.
        // - The image doesn't have an alpha channel.
        if ($this->bg === null || !$image->hasAlpha()) {
            return $image;
        }

        $background = new Color($this->bg);

        if ($background->isTransparent()) {
            return $image;
        }

        $backgroundRGBA = $background->toRGBA();

        if ($image->bands > 2 && $background->hasAlphaChannel()) {
            // If the image has more than two bands and the requested background color has an alpha channel;
            // alpha compositing.

            // Create a new image from a constant that matches the origin image dimensions
            $backgroundImage = $image->newFromImage([
                $backgroundRGBA[0],
                $backgroundRGBA[1],
                $backgroundRGBA[2],
                $backgroundRGBA[3]
            ]);

            // Ensure overlay is premultiplied sRGB
            $backgroundImage = $backgroundImage->premultiply();

            // Premultiply image alpha channel before background transformation
            if (!$this->isPremultiplied) {
                $image = $image->premultiply();
                $this->isPremultiplied = true;
            }

            /*
             * Alpha composite src over dst
             * Assumes alpha channels are already premultiplied and will be unpremultiplied after
             */
            $image = $backgroundImage->composite($image, [BlendMode::OVER], ['premultiplied' => true]);
        } else {
            // If it's a 8bit-alpha channel image or the requested background color hasn't an alpha channel;
            // then flatten the alpha out of an image, replacing it with a constant background color.
            $backgroundColor = [
                $backgroundRGBA[0],
                $backgroundRGBA[1],
                $backgroundRGBA[2]
            ];

            if ($image->bands < 3) {
                // Convert sRGB to greyscale
                $backgroundColor = (0.2126 * $backgroundRGBA[0]) +
                    (0.7152 * $backgroundRGBA[1]) +
                    (0.0722 * $backgroundRGBA[2]);
            }

            // Flatten on premultiplied images causes weird results
            // so unpremultiply if we have a premultiplied image.
            if ($this->isPremultiplied) {
                // Unpremultiply image alpha and cast pixel values to integer
                $image = $image->unpremultiply()->cast(BandFormat::UCHAR);

                $this->isPremultiplied = false;
            }

            $image = $image->flatten([
                'background' => $backgroundColor
            ]);
        }

        return $image;
    }
}
