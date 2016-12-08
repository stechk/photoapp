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

    //TODO zmenit url a typy
    const INNER_URL = 'http://fotoapp.local';
    const OUTER_URL = 'http://fotoapp2.local';

    public static $urlsToActions = [
        self::INNER_URL => 'default',
        self::OUTER_URL => 'notDefault'
    ];

    const TYPE_MEASUREMENT = 'zamereni';
    const TYPE_CONSTRUCT = 'montaz';
    const TYPE_SERVICE = 'servis';
    const TYPE_EXPEDITION = 'expedice';

    /**
     * Allowed types
     * @var array
     */
    private $allowedTypesByUrl = [self::INNER_URL => [
        ["id" => self::TYPE_CONSTRUCT, "name" => "Montáž"],
        ["id" => self::TYPE_SERVICE, "name" => "Servis"],
        ["id" => self::TYPE_MEASUREMENT, "name" => "Zaměření"],
        ],
        self::OUTER_URL => [["id" => self::TYPE_EXPEDITION, "name" => "Expedice"],]
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
    public function isAllowedParameter($type, $url)
    {
        foreach ($this->allowedTypesByUrl as $allowedUrl => $allowedTypes) {
            if ($url == $allowedUrl){
                foreach ($allowedTypes as $item){
                    if ($item["id"] == $type) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * @param $type
     * @return mixed
     */
    public function getTypeByName($type, $url)
    {
        foreach ($this->allowedTypesByUrl as $allowedUrl => $allowedTypes) {
            if ($url == $allowedUrl){
                foreach ($allowedTypes as $item){
                    if ($item["id"] == $type) {
                        return $item;
                    }
                }
            }
        }
    }

    /**
     * @param $op
     * @return \Nette\Database\ResultSet
     */
    public function findPhotoByOp($op)
    {
        return $this->database->query('SELECT i.*, DATE_FORMAT(i.timestamp, "%Y-%m-%d") AS formatted_date 
                                        FROM images as i WHERE i.op = ?', $op);

    }

    public function saveImage($data)
    {
        $this->database->query('INSERT INTO images', $data);
    }

}