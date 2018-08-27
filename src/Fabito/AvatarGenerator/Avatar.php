<?php

namespace Fabito\AvatarGenerator;

use Exception;
use Intervention\Image\ImageManager;
use Intervention\Image\Image;

class Avatar
{
    /**
     * @var string The Imagick driver
     */
    const DRIVER_IMAGICK = 'imagick';

    /**
     * @var string The GD driver
     */
    const DRIVER_GD = 'gd';

    /**
     * @var array $colors Contains available background colors which are randomly choosen when generating a new avatar
     */
    protected $backgroundColors = [
        '#545C96', '#5DA85F', '#62BDB8',
        '#BA9D4C', '#AB5454', '#AD61AC',
        '#7761B0', '#D188AF', '#658DC2',
        '#5EAD83', '#6EAD68', '#D6AD67',
    ];

    /**
     * @var string|null $backgroundColor The background color of the image in case it was set
     */
    protected $backgroundColor = null;

    /**
     * @var string $fontColor The font color which is used for generating the text on the image
     */
    protected $fontColor = '#F7F7F7';

    /**
     * @var int $fontSize The size of the font on the avatar
     */
    protected $fontSize = 50;

    /**
     * @var int $width The width of the avatar
     */
    protected $width = 100;

    /**
     * @var int $height The height of the avatar
     */
    protected $height = 100;

    /**
     * @var int $stringLength The length of the string used to put on the avatar
     */
    protected $stringLength = 2;

    /**
     * @var string|null $string The string printed on the avatar
     */
    protected $string = null;

    /**
     * @var string|null $name In case a name was provided, we safe it here
     */
    protected $name = null;

    /**
     * @var bool $rounded Whether the avatar is rounded or not
     */
    protected $rounded = false;

    /**
     * @var string $font The font which is used with the text on the avatar
     */
    protected $font = '../lib/fonts/OpenSans-Light.ttf';

    /**
     * @var ImageManager|null $imageManager Holds the intervention image manager in case user needs access to it
     */
    protected $imageManager = null;

    /**
     * @var string $imageManagerDriver The driver we use at the image manager
     */
    protected $imageManagerDriver = self::DRIVER_GD;


    /**
     * AvatarGenerator constructor.
     */
    public function __construct()
    {
        $this->imageManager = new ImageManager(['driver' => $this->imageManagerDriver]);
        return $this;
    }

    /**
     * Sets a new driver for the image generation and re-created the image manager
     * @param string $driver
     * @return $this
     * @throws Exception
     */
    public function driver($driver = self::DRIVER_GD)
    {
        $availableDrivers = [self::DRIVER_GD, self::DRIVER_IMAGICK];

        if(!in_array($driver, $availableDrivers)) {
            throw new Exception('Unsupported driver supplied. GD and Imagick are supported only.');
        }

        // Check if imagic is enabled
        if($driver == self::DRIVER_IMAGICK) {
            if(extension_loaded(self::DRIVER_IMAGICK))
                $this->imageManager = new ImageManager(['driver' => self::DRIVER_IMAGICK]);
            else
                throw new Exception('Imagick driver selected, but currently not installed');
        }

        // Check if GD is enabled
        if($driver == self::DRIVER_GD) {
            if(extension_loaded(self::DRIVER_GD))
                $this->imageManager = new ImageManager(['driver' => self::DRIVER_GD]);
            else
                throw new Exception('GD driver selected, but currently not installed');
        }

        return $this;
    }

    /**
     * Sets the height for the generated avatar
     * @param int $height
     * @return $this
     */
    public function height($height = 0)
    {
        $this->height = $this->convertValueToInteger($height);
        return $this;
    }

    /**
     * Sets the width for the generated avatar
     * @param int $width
     * @return $this
     */
    public function width($width = 0)
    {
        $this->width = $this->convertValueToInteger($width);
        return $this;
    }

    /**
     * Sets the dimensions for the avatar in case the default is not used
     * @param int $width
     * @param int $height
     * @return $this
     */
    public function dimensions($width = 0, $height = 0)
    {
        $this->width($width);
        $this->height($height);
        return $this;
    }

    /**
     * An alternative version for dimensions or setting the sizes yourself
     * @param $size
     * @return $this
     */
    public function size($size)
    {
        $this->width($size);
        $this->height($size);
        return $this;
    }

    /**
     * Sets the background color for the avatar
     * @param $color
     * @return $this
     */
    public function backgroundColor($color)
    {
        $this->backgroundColor = $color;
        return $this;
    }

    /**
     * Sets the font color for the given avatar
     * @param $color
     * @return $this
     */
    public function fontColor($color)
    {
        $this->fontColor = $color;
        return $this;
    }

    /**
     * The font size used on the avatar
     * @param $size
     * @return $this
     */
    public function fontSize($size)
    {
        $this->fontSize = $size;
        return $this;
    }

    /**
     * The font family used on the avatars
     * @param $font
     * @return $this
     */
    public function font($font)
    {
        $this->font = $font;
        return $this;
    }

    /**
     * In case a user wants to choose a name instead of a string
     * @param $name
     * @return $this
     */
    public function name($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Uses a string to be displayed on the avatar instead
     * @param $string
     * @return $this
     */
    public function string($string)
    {
        $this->string = $string;
        return $this;
    }

    /**
     * Sets the length of the displayed string
     * @param $length
     * @return $this
     */
    public function length($length)
    {
        $this->stringLength = $length;
        return $this;
    }

    /**
     * Displays a rounded avatar instead of a normal one
     * @return $this
     */
    public function rounded()
    {
        $this->rounded = true;
        return $this;
    }

    /**
     * Creates a WEBP string from the image
     * @param int $quality
     * @return Image
     */
    public function toWebp($quality = 100) : Image
    {
        return $this->createImageCanvas()->encode('webp', $quality);
    }

    /**
     * Creates a PNG string from the image
     * @return Image
     */
    public function toPng() : Image
    {
        return $this->createImageCanvas()->encode('png');
    }

    /**
     * Creates a JPEG string from the image
     * @param int $quality
     * @return Image
     */
    public function toJpeg($quality = 100) : Image
    {
        return $this->createImageCanvas()->encode('jpeg', $quality);
    }

    /**
     * Converts the avatar to a Base64 string
     * @return Image
     */
    public function toBase64() : Image
    {
        return $this->createImageCanvas()->encode('data-url');
    }

    /**
     * Returns the image canvas from the original image manager
     * @return Image
     */
    public function getImageCanvas()
    {
        return $this->createImageCanvas();
    }

    /**
     * Returns the image manager in case additional work is required
     * @return ImageManager|null
     */
    public function getImageManager(): ImageManager
    {
        return $this->imageManager;
    }

    /**
     * Returns the image canvas which we can use to output, stream or do additional work with it
     * @return Image
     */
    protected function createImageCanvas(): Image
    {
        if(!is_null($this->backgroundColor))
            $backgroundColor = $this->backgroundColor;
        else
            $backgroundColor = $this->backgroundColors[array_rand($this->backgroundColors)];

        $image = $this->imageManager->canvas(
            $this->width,
            $this->height,
            !$this->rounded ? $backgroundColor : null
        );

        if($this->rounded) {
            $image = $image->circle($this->width - 2, $this->width / 2, $this->height / 2, function ($img) use ($backgroundColor) {
                return $img->background($backgroundColor);
            });
        }

        $family = $this->font;
        $size = $this->fontSize;
        $color = $this->fontColor;

        $image = $image->text($this->getImageString(), $this->width/2, $this->height/2, function ($font) use ($color, $family, $size) {
            $font->file($family);
            $font->size($size);
            $font->color($color);
            $font->align('center');
            $font->valign('center');
        });

        return $image;
    }

    /**
     * Splits the image string in case required and also in case a name was provided
     * @return bool|null|string
     */
    protected function getImageString()
    {
        if(!is_null($this->name)) {
            $splitName = explode(' ', $this->name);
            $string = '';
            foreach($splitName as $part) {
                $string = $string . mb_substr($part, 0, 1);
            }
            return mb_substr($string, 0, $this->stringLength);
        }

        if(!is_null($this->string)) {
            return mb_substr($this->string, 0, $this->stringLength);
        }

        return null;
    }

    /**
     * Removes additional overhead from any provided value
     * @param $value
     * @return int
     */
    protected function convertValueToInteger($value)
    {
        // If we received a string, remove potential size values
        if(!is_float($value) && !is_int($value)) {
            $value = str_replace(['px', 'em', 'rem'], '', $value);
        }

        return (int) $value;
    }
}