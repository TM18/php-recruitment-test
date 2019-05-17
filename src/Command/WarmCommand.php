<?php

namespace Snowdog\DevTest\Command;

use Snowdog\DevTest\Component\CacheWarmerResolver;
use Snowdog\DevTest\Model\Page;
use Snowdog\DevTest\Model\PageManager;
use Snowdog\DevTest\Model\Varnish;
use Snowdog\DevTest\Model\VarnishManager;
use Snowdog\DevTest\Model\WebsiteManager;
use Symfony\Component\Console\Output\OutputInterface;

class WarmCommand
{
    const DATE_FORMAT = 'Y-m-d H:i:s';

    /**
     * @var WebsiteManager
     */
    private $websiteManager;

    /**
     * @var PageManager
     */
    private $pageManager;

    /**
     * @var VarnishManager
     */
    private $varnishManager;

    /**
     * WarmCommand constructor.
     * @param WebsiteManager $websiteManager
     * @param PageManager $pageManager
     * @param VarnishManager $varnishManager
     */
    public function __construct(WebsiteManager $websiteManager, PageManager $pageManager, VarnishManager $varnishManager)
    {
        $this->websiteManager = $websiteManager;
        $this->pageManager = $pageManager;
        $this->varnishManager = $varnishManager;
    }

    /**
     * @param $id
     * @param OutputInterface $output
     */
    public function __invoke($id, OutputInterface $output)
    {
        $website = $this->websiteManager->getById($id);
        if ($website) {
            $pages = $this->pageManager->getAllByWebsite($website);
            $varnishes = $this->varnishManager->getByWebsite($website);

            $actor = new \Old_Legacy_CacheWarmer_Actor();
            $actor->setActor(function ($hostname, $ip, $url) use ($output) {
                $output->writeln('Visited <info>http://' . $hostname . '/' . ($url != '/' ? $url : '') . '</info> via IP: <comment>' . $ip . '</comment>');
            });
            $warmer = new \Old_Legacy_CacheWarmer_Warmer();
            $warmer->setHostname($website->getHostname());
            $warmer->setActor($actor);

            if (empty($varnishes)) {
                $warmer->setResolver(new \Old_Legacy_CacheWarmer_Resolver_Method());
                $this->warm($pages, $warmer, $output);
            } else {
                /** @var Varnish $varnish */
                foreach ($varnishes as $varnish) {
                    $warmer->setResolver(new CacheWarmerResolver($varnish->getIp()));
                    $this->warm($pages, $warmer, $output);
                }
            }
        } else {
            $output->writeln('<error>Website with ID ' . $id . ' does not exists!</error>');
        }
    }

    /**
     * @param array $pages
     * @param \Old_Legacy_CacheWarmer_Warmer $warmer
     * @param OutputInterface $output
     */
    private function warm($pages, $warmer, $output): void
    {
        /** @var Page $page */
        foreach ($pages as $page) {
            $warmer->warm($page->getUrl());
            $updated = $this->pageManager->updateLastPageVisit($page, date(self::DATE_FORMAT, time()));
            if (!$updated) {
                $output->writeln('<error>Error updating page with ID ' . $page->getPageId() . '</error>');
            }
        }
    }
}