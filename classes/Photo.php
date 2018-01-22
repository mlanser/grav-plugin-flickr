<?php

namespace Grav\Plugin\Flickr2;

require_once(__DIR__.'/../classes/Common.php');

use Grav\Plugin\Flickr2\Common;
use Grav\Common\GravTrait;

class Photo
{
    use GravTrait;

    protected $info;

    /**
     * Constructor.
     *
     * @param $info
     */
    public function __construct($info)
    {
        $this->info = $info;
    }

    /**
     * Get Flickr photo/set/album/collection ID
     *
     * @return string
     */
    public function id()
    {
        return $this->info['id'];
    }

    /**
     * Get Flickr photo title
     *
     * @return string
     */
    public function title()
    {
        return $this->content($this->info['title']);
    }

    /**
     * Get Flickr photo/set description
     *
     * @return string
     */
    public function description()
    {
        return $this->content($this->info['description']);
    }

    /**
     * Get Flickr photo 'date taken'
     *
     * @return string
     */
    public function datetaken()
    {
        return $this->content($this->info['datetaken']);
    }

    /**
     * Create Flickr photo URL
     *
     * @param $format
     * @return string
     */
    public function url($format)
    {
        //(self::getGrav()['debugger'])->addMessage($this->info);

        if ($format == 'o' && !empty($this->info['originalsecret'])) {
            return $this->photoBaseURL() . $this->info['originalsecret'] . '_o.' . $this->info['originalformat'];
        }

        if (Common::isValidPhotoFormat($format)) {
            return $this->photoBaseURL() . $this->info['secret'] . '_' . $format . '.jpg';
        }

        return $this->photoBaseURL() . $this->info['secret'] . '.jpg';
    }

    /**
     * Generate URL to Flickr page for photo/set/album/collection
     *
     * @return string
     */
    public function flickrPage()
    {
        return 'https://www.flickr.com/photos/' . $this->username() . '/' . $this->info['id'];
    }

    /**
     * Get ???
     *
     * @param $val
     * @return mixed
     */
    protected function content($val)
    {
        return is_array($val) ? $val['_content'] : $val;
    }

    /**
     * Get Flicker username/NSID for owner of photo/set
     *
     * @return string
     */
    protected function username()
    {
        if (!empty($this->info['ownername'])) {
            return $this->info['ownername'];
        }

        if (!empty($this->info['owner']['path_alias']) && is_string($this->info['owner']['path_alias'])) {
            return $this->info['owner']['path_alias'];
        }

        return $this->getNSID();
    }

    /**
     * Get Flickr NSID for owner of images. If we can't determine NSID from photo info,
     * then we'll use 'flickr_user_id' from our plugin config YAML as fallback option.
     *
     * @return string
     */
    protected function getNSID()
    {
        if (!empty($this->info['owner']['nsid'])) {
            return $this->info['owner']['nsid'];
        }

        if (!empty($this->info['owner']) && is_string($this->info['owner'])) {
            return $this->info['owner'];
        }

        return (self::getGrav()['config'])->get('plugins.flickr2.flickr_user_id');
    }

    /**
     * Create Flickr photo base URL. This segment is common for all photo formats, etc.
     *
     * @return string
     */
    protected function photoBaseURL()
    {
        return 'https://farm'. $this->info['farm'] .'.staticflickr.com/'. $this->info['server'] .'/'. $this->info['id'] .'_';
    }

}
 
