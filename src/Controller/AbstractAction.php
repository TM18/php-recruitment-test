<?php

namespace Snowdog\DevTest\Controller;

/**
 * Class AbstractAction
 * @package Snowdog\DevTest\Controller
 */
class AbstractAction
{
    /**
     * @param string $location
     * @param string $msg
     */
    protected function redirect(string $location, string $msg = '')
    {
        if (!empty($msg)) {
            $_SESSION['flash'] = $msg;
        }

        return header("Location: $location");
    }
}