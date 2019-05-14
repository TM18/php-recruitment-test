<?php

namespace Snowdog\DevTest\Controller;

use Snowdog\DevTest\Model\UserManager;
use Snowdog\DevTest\Model\VarnishManager;

class CreateVarnishAction
{
    /**
     * @var VarnishManager
     */
    private $varnishManager;

    public function __construct(UserManager $userManager, VarnishManager $varnishManager)
    {
        $this->userManager = $userManager;
        $this->varnishManager = $varnishManager;
    }

    public function execute()
    {
        $ip = $_POST['ip'];

        // TODO - add module logic here

        header('Location: /varnish');
    }
}