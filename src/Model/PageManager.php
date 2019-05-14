<?php

namespace Snowdog\DevTest\Model;

use Snowdog\DevTest\Core\Database;

class PageManager
{

    /**
     * @var Database|\PDO
     */
    private $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function getAllByWebsite(Website $website)
    {
        $websiteId = $website->getWebsiteId();
        /** @var \PDOStatement $query */
        $query = $this->database->prepare('SELECT * FROM pages WHERE website_id = :website');
        $query->bindParam(':website', $websiteId, \PDO::PARAM_INT);
        $query->execute();
        return $query->fetchAll(\PDO::FETCH_CLASS, Page::class);
    }

    public function create(Website $website, $url)
    {
        $websiteId = $website->getWebsiteId();
        /** @var \PDOStatement $statement */
        $statement = $this->database->prepare('INSERT INTO pages (url, website_id) VALUES (:url, :website)');
        $statement->bindParam(':url', $url, \PDO::PARAM_STR);
        $statement->bindParam(':website', $websiteId, \PDO::PARAM_INT);
        $statement->execute();
        return $this->database->lastInsertId();
    }

    /**
     * @param Page $page
     * @param string $date
     * @return bool
     */
    public function updateLastPageVisit(Page $page, string $date): bool
    {
        $statement = $this->database->prepare('UPDATE pages SET last_page_visit = :date WHERE page_id = :page_id');
        $statement->bindParam(':date', $date, \PDO::PARAM_STR);
        $statement->bindParam(':page_id', $page->getPageId(), \PDO::PARAM_INT);
        return $statement->execute();
    }

    /**
     * @param User $user
     * @return array
     */
    public function getStats(User $user): array
    {
        $userId = $user->getUserId();

        return [
            'Total count' => $this->getPagesCount($userId),
            'Most recently visited' => $this->getMostRecentlyVisited($userId),
            'Least recently visited' => $this->getLeastRecentlyVisited($userId)
        ];
    }

    /**
     * @param int $userId
     * @return int
     */
    public function getPagesCount(int $userId): int
    {
        $query = $this->database->prepare('SELECT count(1) FROM websites AS w LEFT JOIN pages AS p ON p.website_id = w.website_id WHERE w.user_id = :user_id');
        $query->bindParam('user_id', $userId, \PDO::PARAM_INT);
        $query->execute();
        return $query->fetchColumn();
    }

    /**
     * @param int $userId
     * @return string
     */
    public function getMostRecentlyVisited(int $userId): string
    {
        $query = $this->database->prepare('SELECT w.hostname, p.url FROM websites AS w LEFT JOIN pages AS p ON w.website_id = p.website_id WHERE w.user_id = :user_id AND p.last_page_visit IS NOT NULL ORDER BY p.last_page_visit DESC LIMIT 1');
        return $this->getRecentlyVisited($query, $userId);
    }

    /**
     * @param int $userId
     * @return string
     */
    public function getLeastRecentlyVisited(int $userId): string
    {
        $query = $this->database->prepare('SELECT w.hostname, p.url FROM websites AS w LEFT JOIN pages AS p ON w.website_id = p.website_id WHERE w.user_id = :user_id AND p.last_page_visit IS NOT NULL ORDER BY p.last_page_visit ASC LIMIT 1');
        return $this->getRecentlyVisited($query, $userId);
    }

    /**
     * @param \PDOStatement $query
     * @param int $userId
     * @return string
     */
    private function getRecentlyVisited(\PDOStatement $query, int $userId): string
    {
        $query->bindParam(':user_id', $userId, \PDO::PARAM_INT);
        $query->execute();
        $result = $query->fetch();

        if (isset($result['hostname']) && isset($result['url'])) {
            return sprintf('%s/%s', $result['hostname'], $result['url']);
        }

        return '';
    }
}