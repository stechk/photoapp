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
     * PhotoModel constructor.
     * @param Connection $database
     */
    public function __construct(Connection $database)
    {
        $this->database = $database;
    }

    public function findPhotoByOp($values)
    {
        return $this->database->query('SELECT * FROM images as i WHERE i.op = ?', $values);
    }

    public function saveImage($data)
    {
        $this->database->query('INSERT INTO images', $data);
    }

}