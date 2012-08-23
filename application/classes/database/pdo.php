<?php defined('SYSPATH') OR die('No direct script access.');

// I overwrote the class to get access to the connection property
// and the REAL prepare functionality...

class Database_PDO extends Kohana_Database_PDO {

    /**
     * Getter for the PDO-connection
     * @return PDO
     */
    public function connection() {
        return $this->_connection;
    }

    /**
     * Prepares a statement for execution and returns a statement object
     * @param string $statement
     * @param array $driver_options
     * @return PDOStatement
     *
     * @see http://php.net/manual/de/pdo.prepare.php
     */
    public function prepare($statement, array $driver_options = array()) {
        return $this->_connection->prepare($statement);
    }

}