<?php

class Database
{
    private $db_host = 'localhost';
    private $db_username = 'root';
    private $db_name = 'tickets_shop';
    private $db_password = '';

    public function db_connection()
    {
        try {
            $connection = new PDO('mysql:host='.$this->db_host.';dbname='.$this->db_name, $this->db_username, $this->db_password);

            $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $connection;
        } catch (PDOException $e) {
            echo 'Connection error: '.$e->getMessage();
            exit;
        }
    }
}