<?php

namespace Snowdog\DevTest\Command;

use Snowdog\DevTest\Model\UserManager;
use Snowdog\DevTest\Service\SitemapService;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ImportSitemapCommand
 * @package Snowdog\DevTest\Command
 */
class ImportSitemapCommand
{
    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * @var SitemapService
     */
    private $sitemapService;

    /**
     * ImportSitemapCommand constructor.
     * @param UserManager $userManager
     * @param SitemapService $sitemapService
     */
    public function __construct(UserManager $userManager, SitemapService $sitemapService)
    {
        $this->userManager = $userManager;
        $this->sitemapService = $sitemapService;
    }

    /**
     * @param string $username
     * @param string $path
     * @param OutputInterface $output
     */
    public function __invoke(string $username, string $path, OutputInterface $output)
    {
        $user = $this->userManager->getByLogin($username);
        if (!$user) {
            echo 'No such user exists.' . PHP_EOL;
            return;
        }

        $result = $this->sitemapService->import($path, $user);

        if ($result['success'] === true) {
            $output->writeln($result['message']);
        } else {
            $output->writeln('<error>' . $result['message'] . '</error>');
        }
    }
}