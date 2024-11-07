<?php

namespace App\helpers;

use Intervention\Image\ImageManager;

class ImageHelper
{
    public static function resize($image, $width = 400, $height = 400, $format = 'jpg', $quality = 90)
    {
        $manager = new ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
        $image = $manager->read($image);
        $image->resize($width, $height);
        return $image;
    }
}
