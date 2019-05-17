<?php

namespace Snowdog\DevTest\Controller;

use Snowdog\DevTest\Model\User;
use Snowdog\DevTest\Model\UserManager;
use Snowdog\DevTest\Model\VarnishManager;

/**
 * Class CreateVarnishAction
 * @package Snowdog\DevTest\Controller
 */
class CreateVarnishAction extends AbstractAction
{
    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * @var VarnishManager
     */
    private $varnishManager;

    /**
     * @var User
     */
    private $user;

    /**
     * CreateVarnishAction constructor.
     * @param UserManager $userManager
     * @param VarnishManager $varnishManager
     */
    public function __construct(UserManager $userManager, VarnishManager $varnishManager)
    {
        $this->userManager = $userManager;
        $this->varnishManager = $varnishManager;
        if (isset($_SESSION['login'])) {
            $this->user = $this->userManager->getByLogin($_SESSION['login']);
        }
    }

    public function execute()
    {
        if (!$this->user) {
            return $this->redirect('/login', 'You have to log in to add Varnish.');
        }

        $ip = $_POST['ip'];

        if (empty($ip) || !filter_var($ip, FILTER_VALIDATE_IP)) {
            return $this->redirect('/varnish', 'IP address is invalid.');
        }

        $created = $this->varnishManager->create($this->user, $ip);
        return $this->redirect('/varnish', $created ? 'Varnish added.' : 'Error while adding varnish.');
    }
}