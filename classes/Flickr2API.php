<?php

namespace Grav\Plugin\Flickr2;

require_once(__DIR__.'/Photoset.php');
require_once(__DIR__.'/Collection.php');

use Grav\Common\Grav;
use Grav\Plugin\Flickr2\Photoset;
use Grav\Plugin\Flickr2\Collection;
use Grav\Common\GPM\Response;
use Grav\Common\Cache;

class Flickr2APIException extends \Exception
{
    public function __construct($obj)
    {
        parent::__construct("Error during Flickr2 API call with code ". $obj['code'] .": ". $obj['message'], $obj['code'], null);
    }
}

class Flickr2API
{
    protected $key;
    protected $secret;
    protected $user_id;
    protected $grav;
    protected $config;
    protected $cache;
    protected $cache_duration;

    /**
     * set some instance variable states
     */
    public function __construct()
    {
        $this->grav = Grav::instance();
        $this->config = $this->grav['config'];
        $this->key = $this->config->get('plugins.flickr2.flickr_api_key');
        $this->secret = $this->config->get('plugins.flickr2.flickr_api_secret');        
        $this->user_id = $this->config->get('plugins.flickr2.flickr_user_id');        
        $this->cache_duration = $this->config->get('plugins.flickr2.flickr_cache_duration');        
        $this->cache = new Cache($this->grav);
    }

    /**
     * @param $id
     * @param $params
     * @return \Grav\Plugin\Flickr2\Photoset
     * @throws Flickr2APIException
     */
    public function photoset($id, $params)
    {
        //$this->grav['debugger']->addMessage($params);
        $info = $this->request(['method' => 'flickr.photosets.getInfo', 'photoset_id' => $id ])['photoset'];
        $this->grav['debugger']->addMessage($info);

        $get_photos_params = array_merge(
            [
                "method" => "flickr.photosets.getPhotos",
                "photoset_id" => $id,
                'user_id' => $info['owner'],
                'extras' => 'license,date_upload,date_taken,owner_name,icon_server,original_format,last_update,geo,tags,machine_tags,views,media,description'
            ],
            $this->get_params($params, ['page', 'per_page', 'privacy_filter', 'media']));
        
        $photos = $this->request( $get_photos_params )['photoset'];

        return new Photoset($info, $photos, $this);
    }

    /**
     * @param $id
     * @return Photo
     * @throws Flickr2APIException
     */
    public function photo($id)
    {
        $info = $this->request(['method' => 'flickr.photos.getInfo', 'photo_id' => $id ])['photo'];

        return new Photo($info, $this);
    }

    /**
     * @param $id
     * @return \Grav\Plugin\Flickr2\Collection|null
     * @throws Flickr2APIException
     */
    public function collection($id)
    {
        $info = $this->request(['method' => 'flickr.collections.getTree', 'collection_id' => $id ])['collections'];

        foreach($info['collection'] as $collection) {
            if( ! strpos($collection['id'], $id) ) {
                return new Collection($collection, $this);
            }
        }

        return null; // TODO
    }

    /**
     * @param $params
     * @return bool|mixed|object
     * @throws Flickr2APIException
     */
    protected function request($params) {
        $query = http_build_query(array_merge($params, ['api_key' => $this->key, 'format' => 'php_serial', 'user_id' => $this->user_id]));
        $this->grav['debugger']->addMessage($query);

        $url = 'https://api.flickr.com/services/rest/?' . $query;

        if($this->cache_duration > 0) {
            $obj = $this->cache->fetch($url);
            if($obj) {
                return $obj;
            }
        }

        $obj = unserialize(Response::get($url));
        if($obj["stat"] != "ok") {
            throw new Flickr2APIException($obj);
        }

        if($this->cache_duration > 0) {
            $this->cache->save($url, $obj, $this->cache_duration);
        }

        return $obj;
    }

    /*
    protected function secret_request($params)
    {
        return $this->request(array_merge($params, ['']));
    }
    */

    /**
     * @param $params
     * @param $keys
     * @return array
     */
    protected function get_params($params, $keys) {
        $retval = [];
        foreach($keys as $key) {
            if(array_key_exists($key, $params))
                $retval[$key] = $params[$key];
        }
        return $retval;
    }
}
