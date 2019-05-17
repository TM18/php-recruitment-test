<?php

namespace Snowdog\DevTest\Controller;

class RegisterFormAction extends AbstractAction
{
    public function execute()
    {
        if (isset($_SESSION['login'])) {
            return $this->forbidden();
        }

        require __DIR__ . '/../view/register.phtml';
    }
}