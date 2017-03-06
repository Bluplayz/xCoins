<?php

namespace de\bluplayz\coins\data;

use de\bluplayz\coins\Coins;
use mysqli;
use mysqli_result;

class MySQL
{
    //mysql data
    private $host = "";
    private $port = "";
    private $user = "";
    private $password = "";
    private $db = "";

    /** @var mysqli */
    private $connection = null;

    /** @var Coins */
    private $plugin;

    public function __construct(Coins $plugin)
    {
        $this->plugin = $plugin;

        //load data
        $this->host = $this->plugin->mysqldata["Address"];
        $this->port = $this->plugin->mysqldata["Port"];
        $this->user = $this->plugin->mysqldata["Username"];
        $this->password = $this->plugin->mysqldata["Password"];
        $this->db = $this->plugin->mysqldata["Database"];

        //create tables if not exists
        self::createTables();
    }

    /**
     * connect to mysql
     */
    public function connect()
    {
        $this->connection = new mysqli($this->host, $this->user, $this->password, $this->db, $this->port);

        if ($this->connection->connect_error) {
            $this->plugin->getLogger()->error("Connection to MySQL Database failed, Error: " . $this->connection->connect_error);
            self::disconnect();
            $this->plugin->getServer()->getPluginManager()->disablePlugin($this->plugin);
        }
    }

    /**
     * disconnect from current mysql session
     */
    public function disconnect()
    {
        if ($this->connection->connect_error && $this->connection != null) {
            $this->connection->close();
        }
        $this->connection = null;
    }

    /**
     * check connection
     * @return boolean
     */
    public function isConnected()
    {
        return $this->connection != null;
    }

    /** returns array with data
     *
     * @param $sql
     * @return array
     */
    public function query($sql)
    {
        $data = [];
        self::connect();

        if (self::isConnected()) {
            if ($result = $this->connection->query($sql)) {
                if ($result instanceof mysqli_result) {
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $data[] = $row;
                        }
                    }
                }
            } else {
                $this->plugin->getLogger()->error("MySQL Error: " . mysqli_error($this->connection));
            }
        }

        self::disconnect();
        return $data;
    }

    public function update($sql)
    {
        self::connect();
        if (self::isConnected()) {
            if ($this->connection->query($sql) !== true) {
                $this->plugin->getLogger()->error("MySQL Error: " . mysqli_error($this->connection));
            }
        }
        self::disconnect();
    }

    /**
     * create tables if not exists
     */
    public function createTables()
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS coins
            (
              id INT NOT NULL AUTO_INCREMENT,
              playername VARCHAR(32) NOT NULL,
              coins INT NOT NULL,
              ip VARCHAR(32) NOT NULL,
              cid VARCHAR(64) NOT NULL,
              PRIMARY KEY (id)
            );
        ";
        self::update($sql);
    }
}