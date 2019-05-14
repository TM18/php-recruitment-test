<?php

namespace Snowdog\DevTest\Model;

use Snowdog\DevTest\Core\Database;

class VarnishManager
{

    /**
     * @var Database|\PDO
     */
    private $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function getAllByUser(User $user)
    {
        // TODO: add logic here
    }

    public function getWebsites(Varnish $varnish)
    {
        // TODO: add logic here
    }

    public function getByWebsite(Website $website)
    {
        // TODO: add logic here
    }

    public function create(User $user, $ip)
    {
        // TODO: add logic here
    }

    public function link($varnish, $website)
    {
        // TODO: add logic here
    }

    public function unlink($varnish, $website)
    {
        // TODO: add logic here
    }

}