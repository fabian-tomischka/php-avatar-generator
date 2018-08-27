# FabianTomischka/php-avatar-generator

Display unique avatars based on the initials of your users names. Easy to use with loads of options to extend by yourself.

## Installation
Require from composer and start using it!

````bash
composer require FabianTomischka/php-avatar-generator
````

## Usage
Quick-start with a simple line of code

````php
use Fabito\AvatarGenerator\Avatar;

$generator = new Avatar();
$avatar = $generator->name('Fabian Tomischka')->toJpeg();
````

You can also use a string instead of a given name. However, when providing a string, it will ignore any given whitespaces and always take the characters from the string directly.

````php
// Will result in Fa on the avatar instead of FT
$avatar = $generator->string('Fabian Tomischka')->toJpeg();
````

You can also access the image manager or canvas of the underlying Intervention/Image in case you need it
````php
$manager = $generator->getImageManager();
$canvas = $generator->name('Fabian Tomischka')->getImageCanvas();
````

## Customizing

The package offers multiple ways of adjusting and tweaking the settings.

**driver(_'driver'_)**

The driver which is used for generating the images. Currently supported: gd and imagick. Default: gd
````php
$avatar = $generator->driver($generator::DRIVER_IMAGICK)->toBase64();
````

**height(_'px'_), width(_'px'_), size(_'px'_), dimensions(_'width', 'height'_)**

Change the dimensions of the avatar. Usually size will do the job. Default: 100px
````php
$avatar = $generator->size(100)->toBase64();
````

**backgroundColor(_'color'_)**

In case you do not want to use the default colors provided by the package you can manually set the color here
````php
$avatar = $generator->backgroundColor('#000000')->toBase64();
````

**fontColor(_'color'_)**

Change the font color. Default: #F7F7F7
````php
$avatar = $generator->fontColor('#000000')->toBase64();
````

**fontSize(_'px'_)**

Change the font size on the avatar. Default: 42px
````php
$avatar = $generator->fontSize(42)->toBase64();
````

**length(_'int'_)**

In case you require less or more characters to be on the avatar. Default: 2
````php
// Output: F
$avatar = $generator->name('Fabian Tomischka')->length(1)->toBase64();
$avatar = $generator->string('FT')->length(1)->toBase64();
````

## Loading custom fonts
By default, the package comes with pre-installed OpenSans Light and Regular fonts. They usually do the job. In case you want to change your font, you can do that by calling the font method and providing a path to the new font:

````php
$avatar = $generator->font('/dir/to/the/font.ttf')->name('Fabian Tomischka')->toBase64();
````

Make sure that the font is available as TTF!

## Getting image data
For easy access the package offers multiple direct methods to access the image in different file formats.

````php
$avatar = $generator->name('Fabian Tomischka')->toBase64(); // Base 64
$avatar = $generator->name('Fabian Tomischka')->toPng(); // PNG
$avatar = $generator->name('Fabian Tomischka')->toJpeg(); // JPEG
$avatar = $generator->name('Fabian Tomischka')->toWebp; // WebP
````

As mentioned above, in case you need access to the underlying manager or image, you can access them via

````php
$manager = $generator->getImageManager();
$canvas = $generator->name('Fabian Tomischka')->getImageCanvas();
````

## Rounded avatars

The package potentially offers you an option to generate rounded avatars.
````php
$avatar = $generator->rounded()->name('Fabian Tomischka')->toBase64();
````

However I do highly suggest using border radius in CSS to display your avatar as a circle. Generating rounded avatars will always result in a lower quality.

## Requirements

- PHP 7.1.7 or higher
- Intervention/Image 2.4 or higher
- Imagick extension (in case you do not use the default GD)
