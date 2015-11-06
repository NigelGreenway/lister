<?php
/**
 * Slim Framework (http://slimframework.com)
 *
 * @link      https://github.com/slimphp/Twig-View
 * @copyright Copyright (c) 2011-2015 Josh Lockhart
 * @license   https://github.com/slimphp/Twig-View/blob/master/LICENSE.md (MIT License)
 */
namespace Custom;

use ZackKitzmiller\Tiny;

class ListerTwigExtension extends \Twig_Extension
{
    /**
     * @var \Slim\Flash\Messages
     */
    private $flash;

    public function __construct($flash)
    {
        $this->flash = $flash;
    }

    public function getName()
    {
        return 'slim-custom';
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('flash_bag', [$this, 'getFlash']),
            new \Twig_SimpleFunction('tiny', [$this, 'tiny']),
        ];
    }

    public function getFlash($key)
    {
        return $this->flash->getMessage($key);
    }

    public function tiny($uuid)
    {
        $tiny = new Tiny($uuid);
        return $tiny->from('z4du');
    }
}