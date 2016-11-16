<?php
/**
 * Created by PhpStorm.
 * User: pl
 * Date: 17.08.16
 * Time: 15:07
 */

namespace Model;

use Nette\Database\Connection;

class PhotoModel
{
    /**
     * @var Connection
     */
    private $database;

    const TYPE_MEASUREMENT = 'zamereni';
    const TYPE_CONSTRUCT = 'montaz';
    const TYPE_SERVICE = 'servis';

    /**
     * Allowed types
     * @var array
     */
    private $types = [
        ["id" =>self::TYPE_CONSTRUCT, "name" => "Montáž"],
        ["id" =>self::TYPE_SERVICE, "name" => "Servis"],
        ["id" =>self::TYPE_MEASUREMENT, "name" => "Zaměření"],
    ];

    /**
     * PhotoModel constructor.
     * @param Connection $database
     */
    public function __construct(Connection $database)
    {
        $this->database = $database;
    }

    /**
     * @param $type
     * @return bool
     */
    public function isAllowedParameter($type)
    {
        foreach ($this->types as $item) {
            if ($item["id"] == $type) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $type
     * @return mixed
     */
    public function getTypeByName($type)
    {
        foreach ($this->types as $item) {
            if ($item["id"] == $type) {
                return $item;
            }
        }
    }

    /**
     * @param $op
     * @return \Nette\Database\ResultSet
     */
    public function findPhotoByOp($op)
    {
        return $this->database->query('SELECT * FROM images as i WHERE i.op = ?', $op);
    }

    public function saveImage($data)
    {
        $this->database->query('INSERT INTO images', $data);
    }

}