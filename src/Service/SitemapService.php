<?php

namespace Snowdog\DevTest\Service;

use Exception;
use PDO;
use Snowdog\DevTest\Core\Database;
use Snowdog\DevTest\Model\Page;
use Snowdog\DevTest\Model\PageManager;
use Snowdog\DevTest\Model\User;
use Snowdog\DevTest\Model\WebsiteManager;
use TM\Exception\SitemapImporterException;
use TM\Model\Website;
use TM\SitemapImporter;

/**
 * Class SitemapService
 * @package Snowdog\DevTest\Service
 */
class SitemapService
{
    /**
     * @var Database|PDO
     */
    private $database;

    /**
     * @var WebsiteManager
     */
    private $websiteManager;

    /**
     * @var PageManager
     */
    private $pageManager;

    /**
     * SitemapService constructor.
     *
     * @param Database $database
     * @param WebsiteManager $websiteManager
     * @param PageManager $pageManager
     */
    public function __construct(Database $database, WebsiteManager $websiteManager, PageManager $pageManager)
    {
        $this->database = $database;
        $this->websiteManager = $websiteManager;
        $this->pageManager = $pageManager;
    }

    /**
     * Imports websites and pages from sitemap using library.
     *
     * @param string $path
     * @param User $user
     * @return array
     */
    public function import(string $path, User $user): array
    {
        $result = [
            'success' => false,
            'message' => ''
        ];

        $importer = new SitemapImporter();
        $importer->loadFile($path);

        try {
            $websites = $importer->getWebsites();

            $this->database->beginTransaction();

            /** @var Website $website */
            foreach ($websites as $website) {
                /** @var \Snowdog\DevTest\Model\Website $importedWebsite */
                $importedWebsite = $this->websiteManager->getByHostname($website->getHost());

                if (!$importedWebsite) {
                    $id = $this->websiteManager->create($user, $website->getName(), $website->getHost());
                    $importedWebsite = $this->websiteManager->getById($id);
                } else if ($importedWebsite->getUserId() != $user->getUserId()) {
                    throw new Exception("Website {$website->getHost()} already belongs to another user.");
                }

                $pages = array_map(function(Page $p) { return $p->getUrl(); }, $this->pageManager->getAllByWebsite($importedWebsite));

                foreach ($website->getPages() as $page) {
                    if (!in_array($page, $pages)) {
                        $this->pageManager->create($importedWebsite, $page);
                    }
                }
            }

            $this->database->commit();
            $result = [
                'success' => true,
                'message' => 'All websites and pages imported.'
            ];
        } catch (SitemapImporterException $e) {
            $result['message'] = $e->getMessage();
        } catch (Exception $e) {
            $this->database->rollBack();
            $result['message'] = 'ROLLBACK: ' . $e->getMessage();
        }

        return $result;
    }
}