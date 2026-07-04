<?php
namespace frontend\helpers;

use Yii;
use yii\helpers\VarDumper;
use yii\web\UploadedFile;
use yii\web\HttpException;
use yii\helpers\FileHelper;
use yii\helpers\Url;
use frontend\helpers\GD;

class Image
{
    public static function upload(UploadedFile $fileInstance, $dir = '', $resizeWidth = null, $resizeHeight = null, $resizeCrop = false)
    {
        $fileName = Upload::getUploadPath($dir) . DIRECTORY_SEPARATOR . Upload::getFileName($fileInstance);

        $uploaded = $resizeWidth
            ? self::copyResizedImage($fileInstance->tempName, $fileName, $resizeWidth, $resizeHeight, $resizeCrop)
            : $fileInstance->saveAs($fileName);

        if(!$uploaded){
            throw new HttpException(500, 'Cannot upload file "'.$fileName.'". Please check write permissions.');
        }

        return Upload::getLink($fileName);
    }

    static function thumb($filename, $width = null, $height = null, $crop = true)
    {
        if($filename && file_exists(($filename = $_SERVER['DOCUMENT_ROOT'] . $filename)))
        {
            $info = pathinfo($filename);
//            $thumbName = $info['filename'] . '-' . md5( filemtime($filename) . (int)$width . (int)$height . (int)$crop ) . '.' . $info['extension'];
//            $thumbFile = Yii::getAlias('@webroot') . DIRECTORY_SEPARATOR . Upload::$UPLOADS_DIR . DIRECTORY_SEPARATOR . 'thumbs' . DIRECTORY_SEPARATOR . $thumbName;
//            $thumbWebFile = '/' . Upload::$UPLOADS_DIR . '/thumbs/' . $thumbName;

            $thumbName = $info['filename'] . '-' . md5( filemtime($filename) . (int)$width . (int)$height . (int)$crop ) . '.' . $info['extension'];
            $thumbFile = $_SERVER['DOCUMENT_ROOT'].getenv('STORAGE_URL') . DIRECTORY_SEPARATOR . 'thumbs'. DIRECTORY_SEPARATOR . $thumbName;
            $thumbWebFile = getenv('STORAGE_URL').'/thumbs'. '/'.$thumbName;

            if(file_exists($thumbFile)){
                return $thumbWebFile;
            }
            elseif(FileHelper::createDirectory(dirname($thumbFile), 0777) && self::copyResizedImage($filename, $thumbFile, $width, $height, $crop)){
                return $thumbWebFile;
            }
        }
        return '';
    }
    
    static function thumbFromPath($filename, $width = null, $height = null, $crop = true)
    {
        if($filename && file_exists(($filename = $_SERVER['DOCUMENT_ROOT'].getenv('STORAGE_URL').$filename)))
        {
            if($subFolderList = explode('source/', $filename)){
                if(count($subFolderList) > 1){
                    $subFolderList = $subFolderList[1];
                    
                    if($subFolderList = explode('/', $subFolderList)){
                        if(count($subFolderList) > 1){
                            $subFolder = $subFolderList[0];
                        }
                    }
                }
            }
            
            $info = pathinfo($filename);
            
            $thumbName = $info['filename'] . '-' . md5( filemtime($filename) . (int)$width . (int)$height . (int)$crop ) . '.' . $info['extension'];
            $thumbFile = $_SERVER['DOCUMENT_ROOT'].getenv('STORAGE_URL') . DIRECTORY_SEPARATOR . 'thumbs'. ($subFolder?DIRECTORY_SEPARATOR.$subFolder:'') . DIRECTORY_SEPARATOR . $thumbName;
            $thumbWebFile = getenv('STORAGE_URL').'/thumbs'. ($subFolder?'/'.$subFolder:'') . '/'.$thumbName;
            
            //var_dump([$thumbName, $thumbFile, $thumbWebFile]);
            
            if(file_exists($thumbFile)){
                return $thumbWebFile;
            }
            elseif(FileHelper::createDirectory(dirname($thumbFile), 0777)
                && self::copyResizedImage($filename, $thumbFile, $width, $height, $crop)){
                return $thumbWebFile;
            }
        }
        return '';
    }

    static function copyResizedImage($inputFile, $outputFile, $width, $height = null, $crop = false)
    {
        if (extension_loaded('gd'))
        {
            error_reporting (1);

            $sourceFile = $inputFile;

            if (self::imageFixOrientationAndReloadFile($inputFile, $outputFile)) {
                $sourceFile = $outputFile;
            }

            $image = new GD($sourceFile);

            if($height) {
                if($width && $crop){
                    $image->cropThumbnail($width, $height);
                } else {
                    $image->resize($width, $height);
                }
            } else {
                $image->resize($width);
            }

            return $image->save($outputFile);
        }
        elseif(extension_loaded('imagick'))
        {
            $image = new \Imagick($inputFile);

            if($height && !$crop) {
                $image->resizeImage($width, $height, \Imagick::FILTER_LANCZOS, 1, true);
            }
            else{
                $image->resizeImage($width, null, \Imagick::FILTER_LANCZOS, 1);
            }

            if($height && $crop){
                $image->cropThumbnailImage($width, $height);
            }

            $image = self::image_fix_orientation_imagick(image);

            return $image->writeImage($outputFile);
        }
        else {
            throw new HttpException(500, 'Please install GD or Imagick extension');
        }
    }

    static function imageFixOrientationAndReloadFile($inputFile, $outputFile = null) {
        error_reporting (1);

        $image = imagecreatefromstring(file_get_contents($inputFile));
        $exif = exif_read_data($inputFile);
        $imageNew = null;

        if (!$outputFile) {
            $outputFile = $inputFile;
        }

        if (!empty($exif['Orientation'])) {
            switch ($exif['Orientation']) {
                case 3:
                    $imageNew = imagerotate($image, 180, 0);
                    break;

                case 6:
                    $imageNew = imagerotate($image, -90, 0);
                    break;

                case 8:
                    $imageNew = imagerotate($image, 90, 0);
                    break;
            }
        }

        if ($imageNew) {
            $imageType = exif_imagetype($inputFile);

            if ($imageType == IMAGETYPE_GIF) {
                return imagegif($imageNew, $outputFile);
            } elseif ($imageType == IMAGETYPE_PNG) {
                return imagepng($imageNew, $outputFile);
            } else {
                return imagejpeg($imageNew, $outputFile);
            }
        }

        return false;
    }

    static function image_fix_orientation(&$image, $filename) {
        $exif = exif_read_data($filename);

        if (!empty($exif['Orientation'])) {
            switch ($exif['Orientation']) {
                case 3:
                    $image = imagerotate($image, 180, 0);
                    break;

                case 6:
                    $image = imagerotate($image, -90, 0);
                    break;

                case 8:
                    $image = imagerotate($image, 90, 0);
                    break;
            }
        }
    }

    static function image_fix_orientation_gd(&$image, $filename) {
        $image = imagerotate($image, array_values([0, 0, 0, 180, 0, 0, -90, 0, 90])[@exif_read_data($filename)['Orientation'] ?: 0], 0);
    }

    static function image_fix_orientation_imagick($image) {
        if (method_exists($image, 'getImageProperty')) {
            $orientation = $image->getImageProperty('exif:Orientation');
        } else {
            $filename = $image->getImageFilename();

            if (empty($filename)) {
                $filename = 'data://image/jpeg;base64,' . base64_encode($image->getImageBlob());
            }

            $exif = exif_read_data($filename);
            $orientation = isset($exif['Orientation']) ? $exif['Orientation'] : null;
        }

        if (!empty($orientation)) {
            switch ($orientation) {
                case 3:
                    $image->rotateImage('#000000', 180);
                    break;

                case 6:
                    $image->rotateImage('#000000', 90);
                    break;

                case 8:
                    $image->rotateImage('#000000', -90);
                    break;
            }
        }
    }
}