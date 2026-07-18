<?php

namespace Agmedia\Helpers;

/**
 * Class Database
 * @package Agmedia\Helpers
 */
class Database {

    /**
     * @var string
     */
    private $host;

    /**
     * @var string
     */
    private $user;

    /**
     * @var string
     */
    private $pass;

    /**
     * @var
     */
    private $db;

    /**
     * @var
     */
    private $connection;


    /**
     * Database constructor.
     *
     * @param $db
     *
     * @throws \Exception
     */
    public function __construct($db = DB_DATABASE)
    {
        $this->host = DB_HOSTNAME;
        $this->user = DB_USERNAME;
        $this->pass = DB_PASSWORD;
        $this->db = $db;

        $this->connect();
    }


    /**
     * @param $sql
     *
     * @return bool|\stdClass
     * @throws \Exception
     */
    public function query($sql)
    {
        $query = $this->connection->query($sql);

        if (!$this->connection->errno) {
            if ($query instanceof \mysqli_result) {
                $data = array();

                while ($row = $query->fetch_assoc()) {
                    $data[] = $row;
                }

                $result = new \stdClass();
                $result->num_rows = $query->num_rows;
                $result->row = isset($data[0]) ? $data[0] : array();
                $result->rows = $data;

                $query->close();

                return $result;
            } else {
                return true;
            }
        } else {
            throw new \Exception('Error: ' . $this->connection->error  . '<br />Error No: ' . $this->connection->errno . '<br />' . $sql);
        }
    }


    /**
     *
     */
    public function close()
    {
        mysqli_close($this->connection);
    }


    /**
     * @param $value
     *
     * @return mixed
     */
    public function escape($value) {
        return $this->connection->real_escape_string($value);
    }


    /**
     * @return mixed
     */
    public function getLastId() {
        return $this->connection->insert_id;
    }


    /**
     * @return string
     */
    public function test()
    {
        return "Host information: " . mysqli_get_host_info($this->connection) . PHP_EOL;;
    }


    /**
     * @throws \Exception if connection is not made
     */
    private function connect()
    {
        $this->connection = new \mysqli($this->host, $this->user, $this->pass, $this->db);

        if ($this->connection->connect_error) {
            throw new \Exception('Error: ' . $this->connection->error . '<br />Error No: ' . $this->connection->errno);
        }

        $this->connection->set_charset("utf8");
        $this->connection->query("SET SQL_MODE = ''");
    }
}