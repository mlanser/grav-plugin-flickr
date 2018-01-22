<?php
namespace Grav\Plugin;

use Grav\Common\Plugin;

class Flickr2Plugin extends Plugin
{

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'onShortcodeHandlers' => ['onShortcodeHandlers', 0],
          //'onTwigExtensions' => ['onTwigExtensions', 0],
            'onTwigTemplatePaths' => ['onTwigTemplatePaths', 0],
            'onPluginsInitialized' => ['onPluginsInitialized', 0],
        ];
    }

    /**
     * Initialize configuration
     */
    public function onPluginsInitialized()
    {
        $this->config = $this->grav['config'];
        $this->enable([
            'onPageInitialized' => ['onPageInitialized', 0],
        ]);
    }

    /**
     * Add current directory to twig lookup paths.
     */
    public function onTwigTemplatePaths()
    {
        $this->grav['twig']->twig_paths[] = __DIR__ . '/templates';
    }

    /*
    public function onTwigExtensions()
    {
        require_once(__DIR__ . '/twig/ShortcodeUITwigExtension.php');
        $this->grav['twig']->twig->addExtension(new FlickrTwigExtension());
    }
    */

    /**
     * Initialize ShortCode handlers
     */
    public function onShortcodeHandlers()
    {
        $this->grav['shortcode']->registerAllShortcodes(__DIR__.'/shortcodes');
    }

    /**
     * Add current plugin CSS.
     */
    public function onPageInitialized()
    {
        $this->grav['assets']->addCss('plugin://flickr2/css/flickr2.css');
    }

}
