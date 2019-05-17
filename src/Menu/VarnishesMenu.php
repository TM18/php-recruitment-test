<?php

namespace Snowdog\DevTest\Menu;

/**
 * Class VarnishesMenu
 * @package Snowdog\DevTest\Menu
 */
class VarnishesMenu extends AbstractMenu
{
    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $_SERVER['REQUEST_URI'] == '/varnish';
    }

    /**
     * @return string
     */
    public function getHref(): string
    {
        return '/varnish';
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return 'Varnishes';
    }

    public function __invoke()
    {
        if (isset($_SESSION['login'])) {
            parent::__invoke();
        }
    }
}