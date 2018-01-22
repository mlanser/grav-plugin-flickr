<?php

namespace Grav\Plugin\Shortcodes;

require_once(__DIR__ . '/../classes/Common.php');

use Grav\Common\Utils;
use Thunder\Shortcode\Shortcode\ShortcodeInterface;

use Grav\Plugin\Flickr2\Common;
use Grav\Plugin\Flickr2\Flickr2API;
use Grav\Plugin\Flickr2\Flickr2APIException;
use Grav\Plugin\Flickr2\Photoset;

class Flickr2PhotoShortcode extends Shortcode
{
    public function init()
    {
        $config = $this->grav['config'];

        $this->shortcode->getHandlers()->add(
            'flickr2-photo',
            function (ShortcodeInterface $sc) use ($config) {
                $id = $sc->getParameter('id', '');
                $api = new Flickr2API();

                try {
                    $output = $this->twig->processTemplate('partials/flickr2-photo.html.twig', [
                            'photo' => $api->photo($id),
                            'params' => Common::makeOutputParams($sc->getParameters(), ['content' => $sc->getContent()]),
                        ]);
                } catch (Flickr2APIException $e) {
                    $output = $this->twig->processTemplate('partials/flickr2-error.html.twig', [
                        'message' => ($config->get('plugins.flickr2.flickr_api_error_photo')) ?: $e->getMessage(),
                        'details' => ($config->get('plugins.flickr2.flickr_api_error_details')) ? ['id' => $id, 'error' => $e->getMessage()] : []
                    ]);
                }

                return $output;
            });
    }
}
