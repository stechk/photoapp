<?php
/**
 * Created by PhpStorm.
 * User: pl
 * Date: 02.03.17
 * Time: 8:59
 */

namespace Model;


use Nette\Database\Connection;

class UsersModel
{
    const SALT = 'test';
    /**
     * @var Connection
     */
    private $dbVykaz;

    /**
     * UsersModel
     * @param Connection $dbVykaz
     */
    public function __construct(Connection $dbVykaz)
    {
        $this->dbVykaz = $dbVykaz;
    }



    public function getAllUsers()
    {
        return $this->dbVykaz->query('SELECT * FROM users');
    }

    public function registrate($values){
        $this->dbVykaz->query('INSERT INTO users ?', $values);
    }

    /**
     * vrací hash hesla
     * @param string $string
     * @return string
     */
    public function calculateHash($string)
    {
        return sha1($string . str_repeat(self::SALT, 5));
    }
}
