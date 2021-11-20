<?php

namespace Weserv\Images\Manipulators;

use Jcupitt\Vips\Image;
use Weserv\Images\Manipulators\Helpers\Utils;

/**
 * @property int $rotation
 * @property bool $flip
 * @property bool $flop
 */
class Orientation extends BaseManipulator
{
    /**
     * Perform orientation image manipulation.
     *
     * @param Image $image The source image.
     *
     * @throws \Jcupitt\Vips\Exception
     *
     * @return Image The manipulated image.
     */
    public function run(Image $image): Image
    {
        // Rotate if required.
        if ($this->rotation !== 0) {
            // Need to copy to memory, we have to stay seq.
            $image = $image->copyMemory()->rot('d' . $this->rotation);
        }

        // Flip (mirror about Y axis) if required.
        if ($this->flip) {
            $image = $image->flipver();
        }

        // Flop (mirror about X axis) if required.
        if ($this->flop) {
            $image = $image->fliphor();
        }

        // Remove EXIF Orientation from image, if any
        if ($image->typeof(Utils::VIPS_META_ORIENTATION) !== 0) {
            $image->remove(Utils::VIPS_META_ORIENTATION);
        }

        return $image;
    }
}
