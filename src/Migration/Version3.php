<?php

namespace Snowdog\DevTest\Migration;

use PDO;
use Snowdog\DevTest\Core\Database;

/**
 * Class Version3
 * @package Snowdog\DevTest\Migration
 */
class Version3
{
    /**
     * @var Database|PDO
     */
    private $database;

    /**
     * Version3 constructor.
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function __invoke()
    {
        $this->addLastPageVisit();
    }

    private function addLastPageVisit()
    {
        $alterQuery = <<<SQL
ALTER TABLE `pages`
ADD `last_page_visit` DATETIME
SQL;

        $this->database->exec($alterQuery);
    }
}