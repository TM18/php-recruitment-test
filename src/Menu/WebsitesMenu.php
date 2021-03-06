<?php

namespace Snowdog\DevTest\Menu;

class WebsitesMenu extends AbstractMenu
{

    public function isActive()
    {
        return $_SERVER['REQUEST_URI'] == '/';
    }

    public function getHref()
    {
        return '/';
    }

    public function getLabel()
    {
        return 'Websites';
    }

    public function __invoke()
    {
        if (isset($_SESSION['login'])) {
            parent::__invoke();
        }
    }
}