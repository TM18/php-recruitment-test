<?php

namespace Snowdog\DevTest\Controller;

use Snowdog\DevTest\Model\User;
use Snowdog\DevTest\Model\UserManager;
use Snowdog\DevTest\Service\SitemapService;

/**
 * Class SitemapImportAction
 * @package Snowdog\DevTest\Controller
 */
class SitemapImportAction extends AbstractAction
{
    const FILE_KEY = 'sitemap-file';

    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * @var SitemapService
     */
    private $sitemapService;

    /**
     * @var User
     */
    private $user;

    /**
     * SitemapImportAction constructor.
     * @param UserManager $userManager
     * @param SitemapService $sitemapService
     */
    public function __construct(UserManager $userManager, SitemapService $sitemapService)
    {
        $this->userManager = $userManager;
        $this->sitemapService = $sitemapService;
        if(isset($_SESSION['login'])) {
            $this->user = $this->userManager->getByLogin($_SESSION['login']);
        }
    }

    public function execute()
    {
        if (!$this->user) {
            return $this->redirect('/login', 'You have to log in to import sitemap.');
        }

        if ($_FILES[self::FILE_KEY]['error']) {
            return $this->redirect('/', 'Error during file upload.');
        }

        if ($_FILES[self::FILE_KEY]['type'] != 'text/xml') {
            return $this->redirect('/', 'Invalid file type.');
        }

        $result = $this->sitemapService->import($_FILES[self::FILE_KEY]['tmp_name'], $this->user);

        return $this->redirect('/', $result['message']);
    }
}