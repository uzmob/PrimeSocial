<?php

/**
 * @package     Prime Social
 * @link        http://primesocial.ru
 * @copyright   Copyright (C) 2016 Prime Social
 * @author      BoB | http://primesocial.ru/about
 */


if (!class_exists('PDO')) {
    die('Fatal Error: Ishlash uchun PDO kerak.');
}

/**
 * Class PDO_
 */
class PDO_ extends PDO
{
    /**
     * PDO_ constructor.
     * @param $dsn
     * @param $username
     * @param $password
     */
    public function __construct($dsn, $username, $password)
    {
        parent:: __construct($dsn, $username, $password);
        $this->setAttribute(PDO :: ATTR_ERRMODE, PDO :: ERRMODE_EXCEPTION);
        $this->setAttribute(PDO :: ATTR_DEFAULT_FETCH_MODE, PDO :: FETCH_ASSOC);
    }

    /**
     * @param string $sql
     * @return PDOStatement
    
    public function prepare($sql)
    {
        $stmt = parent:: prepare($sql, array(
            PDO :: ATTR_STATEMENT_CLASS => array('PDOStatement_')
        ));
        return $stmt;
    }
 */
    /**
     * @param string $sql
     * @param array $params
     * @return PDOStatement
     */
    public function query($sql, $params = array())
    {
        $stmt = $this->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * @param $sql
     * @param array $params
     * @return mixed
     */
    public function querySingle($sql, $params = array())
    {
        $stmt = $this->query($sql, $params);
        $stmt->execute($params);
        return $stmt->fetchColumn(0);
    }

    /**
     * @param $sql
     * @param array $params
     * @return mixed
     */
    public function queryFetch($sql, $params = array())
    {
        $stmt = $this->query($sql, $params);
        $stmt->execute($params);
        return $stmt->fetch();
    }
}

/**
 * Class PDOStatement_
 */
class PDOStatement_ extends PDOStatement
{
    /**
     * @param array $params
     * @return $this|bool
     */
    public function execute($params = array())
    {
        if (func_num_args() == 1) {
            $params = func_get_arg(0);
        } else {
            $params = func_get_args();
        }
        if (!is_array($params)) {
            $params = array($params);
        }
        parent:: execute($params);
        return $this;
    }

    /**
     * @return mixed
     */
    public function fetchSingle()
    {
        return $this->fetchColumn(0);
    }

    /**
     * @return array
     */
    public function fetchAssoc()
    {
        $this->setFetchMode(PDO::FETCH_NUM);
        $data = array();
        while ($row = $this->fetch()) {
            $data[$row[0]] = $row[1];
        }
        return $data;
    }
}

/**
 * Class DB
 */
class DB
{
    /** @var PDO_ */
    static $dbs;

    /**
     * DB constructor.
     */
    public function __construct()
    {
        try {
            self:: $dbs = new PDO_('mysql:host=' . DBHOST . ';port=' . DBPORT . ';dbname=' . DBNAME, DBUSER, DBPASS);
            self:: $dbs->exec('SET CHARACTER SET utf8');
            self:: $dbs->exec('SET NAMES utf8');
        } catch (PDOException $e) {
            die('Connection failed: ' . $e->getMessage());
        }
    }
}

$db = new DB();