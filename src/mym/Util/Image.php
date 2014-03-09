<?php

/**
 * Image manipulation
 * @copyright 2014 Mikhail Yurasov <me@yurasov.me>
 */

namespace mym\Util;

class Image
{
  protected $width;
  protected $height;
  protected $jpegQuality = 85;
  protected $doNotEnlarge = true;
  protected $bestFill = false;

  protected $mimeType;
  protected $file;

  public function __construct($file = null)
  {
    $this->file = $file;
  }

  public function resize($width, $height)
  {
    if (!file_exists($this->file)) {
      throw new \Exception("Source file doesn't exist");
    }

    // mime type shouild be set before resizing
    if ($this->getMimeType() == 'image/jpeg') {
      $format = 'jpeg';
    } else {
      if ($this->getMimeType() == 'image/png') {
        $format = 'png';
      } else {
        throw new \Exception(sprintf('Unsupported mime type: "%s"', $this->getMimeType()));
      }
    }

    // resize image

    $im = new \Imagick();
    $im->readImage($this->file);
    $im->setImageFormat($format);

    if ($format == 'jpeg') {
      $im->setImageCompressionQuality($this->jpegQuality);
    }

    // get image dimensions
    $imageWidth = $im->getImageWidth();
    $imageHeight = $im->getImageHeight();

    if ($this->bestFill) { // best fill

      // find the resize ratio
      $widthRatio = $imageWidth / $width;
      $heightRatio = $imageHeight / $height;
      $ratio = min($widthRatio, $heightRatio);

      // find the new dimensions for the image
      $imageWidth = round($imageWidth / $ratio);
      $imageHeight = round($imageHeight / $ratio);

      // resize image
      $im->thumbnailImage($imageWidth, $imageHeight);

      // cut excess
      $excessWidth = $imageWidth - $width;
      $excessHeight = $imageHeight - $height;
      $im->cropImage($width, $height, round($excessWidth / 2), round($excessHeight / 2));

    } else { // best fit

      // do not enlarge images
      if ($this->doNotEnlarge) {
        $width = min($imageWidth, $width);
        $height = min($imageHeight, $height);
      }

      $im->thumbnailImage($width, $height, true);
    }

    // correct orientation
    $this->correctOrientation($im);

    // remove any profiles

    $profiles = ($im->getImageProfiles("*", false));

    for ($i = 0; $i < count($profiles); $i++) {
      $im->profileImage($profiles[$i], null);
    }

    // write to file
    $im->writeImage($this->file);
    clearstatcache(false, $this->file); // update file info

    // get dimensions
    $this->width = $im->getImageWidth();
    $this->height = $im->getImageHeight();

    $im->destroy();
  }

  public function getMimeType()
  {
    if (!$this->mimeType) {
      $this->updateFileInfo();
    }

    return $this->mimeType;
  }

  protected function updateFileInfo()
  {
    if (file_exists($this->file)) {
      // mime type
      if (is_null($this->mimeType)) {
        $fi = finfo_open(\FILEINFO_MIME_TYPE);
        $this->mimeType = finfo_file($fi, $this->file);
        finfo_close($fi);
      }
    } else {
      throw new \Exception('File does not exist');
    }
  }

  /**
   * Corrects orientation of an image
   * @param \Imagick $im
   */

  private function correctOrientation(\Imagick & $im)
  {
    switch ($im->getImageOrientation()) {

      case \Imagick::ORIENTATION_TOPRIGHT:
        $im->flipImage();
        break;

      case \Imagick::ORIENTATION_BOTTOMRIGHT:
        $im->rotateImage(new \ImagickPixel("none"), 180);
        break;

      case \Imagick::ORIENTATION_BOTTOMLEFT:
        $im->rotateImage(new \ImagickPixel("none"), 180);
        $im->flipImage();
        break;

      case \Imagick::ORIENTATION_LEFTTOP:
        $im->rotateImage(new \ImagickPixel("none"), 90);
        $im->flipImage();
        break;

      case \Imagick::ORIENTATION_RIGHTTOP:
        $im->rotateImage(new \ImagickPixel("none"), 90);
        break;

      case \Imagick::ORIENTATION_RIGHTBOTTOM:
        $im->rotateImage(new \ImagickPixel("none"), -90);
        $im->flipImage();
        break;

      case \Imagick::ORIENTATION_LEFTBOTTOM:
        $im->rotateImage(new \ImagickPixel("none"), -90);
        break;

      default:
    }
  }

  public function getWidth()
  {
    if (is_null($this->width)) {
      $this->updateDimensions();
    }

    return $this->width;
  }

  /**
   * Detect image dimensions
   */
  private function updateDimensions()
  {
    if (is_null($this->width) || is_null($this->height)) {

      $im = new \Imagick();

      // read file
      if (is_string($this->file)) {
        $im->readImage($this->file);
      }

      $this->width = $im->getImageWidth();
      $this->height = $im->getImageHeight();
      $im->destroy();
    }
  }

  public function getHeight()
  {
    if (is_null($this->height)) {
      $this->updateDimensions();
    }

    return $this->height;
  }

  public function setHeight($height)
  {
    $this->height = $height;
  }

  public function getBestFill()
  {
    return $this->bestFill;
  }

  public function setBestFill($bestFill)
  {
    $this->bestFill = $bestFill;
  }

  public function getDoNotEnlarge()
  {
    return $this->doNotEnlarge;
  }

  public function setDoNotEnlarge($doNotEnlarge)
  {
    $this->doNotEnlarge = $doNotEnlarge;
  }

  public function getJpegQuality()
  {
    return $this->jpegQuality;
  }

  public function setJpegQuality($jpegQuality)
  {
    $this->jpegQuality = $jpegQuality;
  }

  public function getFile()
  {
    return $this->file;
  }

  public function setFile($file)
  {
    $this->file = $file;
  }

  public function setMimeType($mimeType)
  {
    $this->mimeType = $mimeType;
  }

  public function setWidth($width)
  {
    $this->width = $width;
  }
}