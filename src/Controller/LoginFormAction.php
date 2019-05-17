<?php

namespace Snowdog\DevTest\Controller;

class LoginFormAction extends AbstractAction
{

    public function execute()
    {
        if (isset($_SESSION['login'])) {
            return $this->forbidden();
        }

        require __DIR__ . '/../view/login.phtml';
    }
}