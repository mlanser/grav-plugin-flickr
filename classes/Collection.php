<?php

namespace Grav\Plugin\Flickr2;

require_once(__DIR__ . '/Photoset.php');

use Grav\Plugin\Flickr2\Photoset;
use Grav\Common\GravTrait;

class Collection
{
    use GravTrait;

    protected $info;
    protected $collections;
    protected $sets;

    /**
     * Constructor.
     *
     * @param $tree
     * @param $api
     */
    public function __construct($tree, $api)
    {
        $this->info = $tree;
        $this->collections = [];
        $this->sets = [];

        if (is_array($tree['collection'])) {
            foreach ($tree['collection'] as $collection) {
                $this->collections[] = new Collection($collection, $api);
            }
        }

        if (is_array($tree['set'])) {
            foreach ($tree['set'] as $set) {
                $this->sets[] = $api->photoset($set['id'], []);
            }
        }
    }

    /**
     * Get Flickr collection title
     *
     * @return string
     */
    public function title()
    {
        return $this->info['title'];
    }

    /**
     * Get Flickr collection description
     *
     * @return string
     */
    public function description()
    {
        return $this->info['description'];
    }

    /**
     * Get all Flickr collections
     *
     * @return mixed
     */
    public function collections()
    {
        return $this->collections;
    }

    /**
     * Get all sets in a Flickr collection
     *
     * @return mixed
     */
    public function sets()
    {
        return $this->sets;
    }
}
