<?php

if (! function_exists('settings')) {
    function settings(string $key, $default = null)
    {
        return config('settings.' . $key, $default);
    }
}

if (! function_exists('makeThumbnail')) {
    function makeThumbnail($src, $width = 100, $height = 100)
    {
        if (! is_file($src)) {
            throw new Exception("No valid image provided with {$src}.");
        }

        $arrImageDetails = getimagesize($src);
        $originalWidth = $arrImageDetails[0];
        $originalHeight = $arrImageDetails[1];
        if ($originalWidth > $originalHeight) {
            $newWidth = $width;
            $newHeight = intval($originalHeight * $newWidth / $originalWidth);
        } else {
            $newHeight = $height;
            $newWidth = intval($originalWidth * $newHeight / $originalHeight);
        }
        $destX = intval(($width - $newWidth) / 2);
        $destY = intval(($height - $newHeight) / 2);
        if ($arrImageDetails[2] == IMAGETYPE_GIF) {
            $imgt = "ImageGIF";
            $imgcreatefrom = "ImageCreateFromGIF";
        }
        if ($arrImageDetails[2] == IMAGETYPE_JPEG) {
            $imgt = "ImageJPEG";
            $imgcreatefrom = "ImageCreateFromJPEG";
        }
        if ($arrImageDetails[2] == IMAGETYPE_PNG) {
            $imgt = "ImagePNG";
            $imgcreatefrom = "ImageCreateFromPNG";
        }

        if ($imgt) {
            $oldImage = $imgcreatefrom($src);
            $newImage = imagecreatetruecolor($width, $height);
            imagecopyresized($newImage, $oldImage, $destX, $destY, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
            $pathInfos = pathinfo($src);
            $dest = $pathInfos['dirname'] . '/' .
                $pathInfos['filename'] . '_' .
                $width . 'x' . $height . '.' .
                $pathInfos['extension'];
            $imgt($newImage, $dest);
        }
    }
}
