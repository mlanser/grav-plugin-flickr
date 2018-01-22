<?php

namespace Grav\Plugin\Flickr2;

require_once(__DIR__ . '/Photo.php');

use Grav\Plugin\Flickr2\Photo;
use Grav\Common\GravTrait;

class Photoset
{
    use GravTrait;

    protected $photos;
    protected $info;
    protected $api;

    /**
     * Constructor.
     *
     * @param $info
     * @param $photos
     * @param $api
     */
    public function __construct($info, $photos, $api)
    {
        $this->info = $info;
        $this->photos = $photos;
        $this->api = $api;
    }

    /**
     * Get Flickr photoset/album title
     *
     * @return string
     */
    public function title()
    {
        return $this->info["title"]["_content"];
    }

    /**
     * Get Flickr photoset/album description
     *
     * @return string
     */
    public function description()
    {
        return $this->info["description"]["_content"];
    }

    /**
     * Get all photos in a Flickr photoset/album
     *
     * @return array
     */
    public function photos()
    {
        $photos = [];

        foreach ($this->photos['photo'] as $photo) {
            $photos[] = new Photo($photo);
        }

        return $photos;
    }
}
