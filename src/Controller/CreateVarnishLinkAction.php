<?php

namespace Snowdog\DevTest\Controller;

use Snowdog\DevTest\Model\User;
use Snowdog\DevTest\Model\UserManager;
use Snowdog\DevTest\Model\VarnishManager;

/**
 * Class CreateVarnishLinkAction
 * @package Snowdog\DevTest\Controller
 */
class CreateVarnishLinkAction
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
     * CreateVarnishLinkAction constructor.
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
            $this->sendResponse(401);
            return;
        }

        $jsonData = json_decode(file_get_contents('php://input'), true);

        if (!isset($jsonData['websiteId']) || !isset($jsonData['varnishId']) || !isset($jsonData['isLinked'])) {
            $response = [
                'success' => false,
                'message' => 'Missing parameters'
            ];

            $this->sendResponse(400, $response);
            return;
        }

        $websiteId = intval($jsonData['websiteId']);
        $varnishId = intval($jsonData['varnishId']);
        $isLinked = boolval($jsonData['isLinked']);

        if ($isLinked) {
            $result = $this->varnishManager->unlink($varnishId, $websiteId);
        } else {
            $result = $this->varnishManager->link($varnishId, $websiteId);
        }

        $response = [
            'success' => $result,
            'message' => $result ? 'Action performed successfully.' : 'Something went wrong.'
        ];

        $this->sendResponse($result ? 200 : 500, $response);
    }

    /**
     * @param int $code
     * @param array $body
     */
    private function sendResponse(int $code, array $body = [])
    {
        http_response_code($code);
        echo json_encode($body);
    }
}