<?php
/**
 * Created by PhpStorm.
 * User: stechk
 * Date: 9.1.2017
 * Time: 9:35
 */

namespace Model;

use Nette\Database\Connection;
use Nette\Security as NS;


class UserAuthenticator implements NS\IAuthenticator
{
    const SALT = 'albixon';
    public $database;

    function __construct(Connection $database)
    {
        $this->database = $database;
    }

    function authenticate(array $credentials)
    {
        list($email, $password) = $credentials;
        $row = $this->database->query('SELECT * FROM users WHERE email = ?', $email)->fetch();

        if (!$row) {
            throw new NS\AuthenticationException('Nesprávné jméno nebo heslo');
        }

        if ($this->calculateHash($password) !== $row->password) {
            throw new NS\AuthenticationException('Nesprávné jméno nebo heslo');
        }

        return new NS\Identity($row->users_id, $row->users_roles_id,['full_name' => $row->name . ' '. $row->lastname]);
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