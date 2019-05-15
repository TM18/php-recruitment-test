<?php

namespace Snowdog\DevTest\Migration;

use PDO;
use Snowdog\DevTest\Core\Database;

/**
 * Class Version4
 * @package Snowdog\DevTest\Migration
 */
class Version4
{
    /**
     * @var Database|PDO
     */
    private $database;

    /**
     * Version4 constructor.
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function __invoke()
    {
        $this->createVarnishesTable();
        $this->createVarnishLinksTable();
    }

    private function createVarnishesTable()
    {
        $createQuery = <<<SQL
CREATE TABLE `varnishes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ip` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ip` (`ip`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `varnish_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL;
        $this->database->exec($createQuery);
    }

    private function createVarnishLinksTable()
    {
        $createQuery = <<<SQL
CREATE TABLE `varnish_links` (
  `varnish_id` int(11) unsigned NOT NULL,
  `website_id` int(11) unsigned NOT NULL,
  CONSTRAINT `varnish_links_unique` UNIQUE (`varnish_id`, `website_id`),
  KEY `varnish_id` (`varnish_id`),
  CONSTRAINT `varnish_link_varnish_fk` FOREIGN KEY (`varnish_id`) REFERENCES `varnishes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `website_id` (`website_id`),
  CONSTRAINT `varnish_link_website_fk` FOREIGN KEY (`website_id`) REFERENCES `websites` (`website_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL;
        $this->database->exec($createQuery);
    }
}