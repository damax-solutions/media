<?php

declare(strict_types=1);

namespace Damax\Media\Domain\Image;

use Damax\Media\Domain\Model\File;
use Symfony\Component\HttpFoundation\Response;

abstract class Manipulator
{
    /**
     * Rotates image.
     */
    const ORIENTATION = 'or';

    /**
     * Flips image.
     */
    const FLIP = 'flip';

    /**
     * Crops image to specific dimensions.
     */
    const CROP = 'crop';

    /**
     * Sets image width in pixels.
     */
    const WIDTH = 'w';

    /**
     * Sets image height in pixels.
     */
    const HEIGHT = 'h';

    /**
     * Fit image to its target dimensions.
     */
    const FIT = 'fit';

    /**
     * Multiples overall image size.
     */
    const PIXEL_RATIO = 'dpr';

    /**
     * Adjusts image brightness.
     */
    const BRIGHTNESS = 'bri';

    /**
     * Adjusts image contrast.
     */
    const CONTRAST = 'con';

    /**
     * Adjusts image gamma.
     */
    const GAMMA = 'gam';

    /**
     * Sharpen image.
     */
    const SHARPEN = 'sharp';

    /**
     * Applies blur effect.
     */
    const BLUR = 'blur';

    /**
     * Applies pixelation effect.
     */
    const PIXELATE = 'pixel';

    /**
     * Applies filter effect.
     */
    const FILTER = 'filt';

    /**
     * Sets image background color.
     */
    const BACKGROUND = 'bg';

    /**
     * Adds border.
     */
    const BORDER = 'border';

    /**
     * Defines quality of image.
     */
    const QUALITY = 'q';

    /**
     * Encodes image to specific format.
     */
    const FORMAT = 'fm';

    const ALL = [
        self::ORIENTATION,
        self::FLIP,
        self::CROP,
        self::WIDTH,
        self::HEIGHT,
        self::FIT,
        self::PIXEL_RATIO,
        self::BRIGHTNESS,
        self::CONTRAST,
        self::GAMMA,
        self::SHARPEN,
        self::BLUR,
        self::PIXELATE,
        self::FILTER,
        self::BORDER,
        self::QUALITY,
        self::FORMAT,
    ];

    public static function validParams(array $params): bool
    {
        return array_diff(array_flip($params), self::ALL) ? false : true;
    }

    abstract public function processFile(File $file, array $params): Response;
}
