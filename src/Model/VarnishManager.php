<?php

namespace Snowdog\DevTest\Model;

use PDO;
use Snowdog\DevTest\Core\Database;

/**
 * Class VarnishManager
 * @package Snowdog\DevTest\Model
 */
class VarnishManager
{

    /**
     * @var Database|PDO
     */
    private $database;

    /**
     * VarnishManager constructor.
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    /**
     * @param User $user
     * @return array
     */
    public function getAllByUser(User $user): array
    {
        $userId = $user->getUserId();
        $query = $this->database->prepare('SELECT id, INET_NTOA(ip) AS ip, user_id FROM varnishes WHERE user_id = :user_id');
        $query->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_CLASS, Varnish::class);
    }

    /**
     * @param Varnish $varnish
     * @return array
     */
    public function getWebsites(Varnish $varnish): array
    {
        $varnishId = $varnish->getId();
        $query = $this->database->prepare('SELECT w.* FROM varnish_links vl LEFT JOIN websites w ON vl.website_id = w.website_id WHERE varnish_id = :varnish_id');
        $query->bindParam(':varnish_id', $varnishId);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_CLASS, Website::class);
    }

    /**
     * @param Website $website
     * @return array
     */
    public function getByWebsite(Website $website): array
    {
        $websiteId = $website->getWebsiteId();
        $query = $this->database->prepare('SELECT v.id, INET_NTOA(v.ip) AS ip, v.user_id FROM varnishes v LEFT JOIN varnish_links vl ON v.id = vl.varnish_id WHERE vl.website_id = :website_id');
        $query->bindParam(':website_id', $websiteId, PDO::PARAM_INT);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_CLASS, Varnish::class);
    }

    /**
     * @param User $user
     * @param $ip
     * @return bool
     */
    public function create(User $user, $ip): bool
    {
        $userId = $user->getUserId();
        $statement = $this->database->prepare('INSERT INTO varnishes (ip, user_id) VALUES (INET_ATON(:ip), :user_id)');
        $statement->bindParam(':ip', $ip, PDO::PARAM_STR);
        $statement->bindParam(':user_id', $userId, PDO::PARAM_INT);
        return $statement->execute();
    }

    /**
     * @param int $varnishId
     * @param int $websiteId
     * @return bool
     */
    public function link(int $varnishId, int $websiteId): bool
    {
        $statement = $this->database->prepare('INSERT INTO varnish_links (varnish_id, website_id) VALUES (:varnish_id, :website_id)');
        $statement->bindParam(':varnish_id', $varnishId, PDO::PARAM_INT);
        $statement->bindParam(':website_id', $websiteId, PDO::PARAM_INT);
        return $statement->execute();
    }

    /**
     * @param int $varnishId
     * @param int $websiteId
     * @return bool
     */
    public function unlink(int $varnishId, int $websiteId): bool
    {
        $statement = $this->database->prepare('DELETE FROM varnish_links WHERE varnish_id = :varnish_id AND website_id = :website_id');
        $statement->bindParam(':varnish_id', $varnishId, PDO::PARAM_INT);
        $statement->bindParam(':website_id', $websiteId, PDO::PARAM_INT);
        return $statement->execute();
    }

}