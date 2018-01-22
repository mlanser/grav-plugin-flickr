<?php

namespace Grav\Plugin\Flickr2;

require_once(__DIR__.'/Flickr2API.php');

class Common
{
    /**
     * Get default values for formatting parameters
     *
     * @return array
     */
    static function defaultParams()
    {
        return [
            'photoFormat'        => 's',
            'lightboxFormat'     => 'z',
            'photosetTitleTag'   => 'h4',
            'photosetDescTag'    => 'h5',
            'collectionTitleTag' => 'h3',
            'collectionDescTag'  => 'h5'
        ];
    }

    /**
     * Get
     * @return array
     */
    static function validPhotoFormats()
    {
        return [
            's', // small square 75x75
            'q', // large square 150x150
            't', // thumbnail, 100 on longest side
            'm', // small, 240 on longest side
            'n', // small, 320 on longest side
            'z', // medium 640, 640 on longest side
            'c', // medium 800, 800 on longest side
            'b', // large, 1024 on longest side
            'h', // large 1600, 1600 on longest side
            'k', // large 2048, 2048 on longest side
        ];
    }

    /**
     * Verify that value is a valid Flickr photo format
     *
     * @param $format
     * @return bool
     */
    static function isValidPhotoFormat($format)
    {
        return in_array($format, self::validPhotoFormats());
    }

    /**
     * Creates proper array of parameters for Twig output by merging
     * them with a default parameter array. Each parameter must be
     * an associative array.
     *
     * @param array ...$params
     * @return array
     */
    static function makeOutputParams(...$params)
    {
        $outArr = self::defaultParams();

        foreach ($params as $prm) {
            if (!empty($prm) && is_array($prm)) {
                $outArr = array_merge($outArr, $prm);
            }
        }

        return $outArr;
    }
}
