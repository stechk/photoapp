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

    const TYPE_MEASUREMENT = 'zamereni';
    const TYPE_CONSTRUCT = 'montaz';
    const TYPE_SERVICE = 'servis';
    const TYPE_EXPEDITION = 'expedice';

    private $domainInternal;
    private $domainExternal;

    /**
     * @var Connection
     */
    private $database;

    /**
     * Povolene kategorie (typy) fotek dle domeny
     * @var array
     */
    private $allowedTypesByUrl;


    /**
     * PhotoModel constructor.
     * @param Connection $database
     */
    public function __construct(Connection $database, $domainInternal, $domainExternal)
    {
        $this->database = $database;
        $this->domainInternal = $domainInternal;
        $this->domainExternal = $domainExternal;

        $this->allowedTypesByUrl = [
            $this->domainInternal["domain"] => [
                ["id" => self::TYPE_CONSTRUCT, "name" => "Montáž"],
                ["id" => self::TYPE_SERVICE, "name" => "Servis"],
                ["id" => self::TYPE_MEASUREMENT, "name" => "Zaměření"],
            ],
            $this->domainExternal["domain"] => [
                ["id" => self::TYPE_EXPEDITION, "name" => "Expedice"],
            ]
        ];
    }


    public function getDomainAction($domain)
    {
        $domain = str_replace("http://", "", $domain);
        if ($domain == $this->domainExternal["domain"]) {
            return $this->domainExternal["action"];
        } elseif ($domain == $this->domainInternal["domain"]) {
            return $this->domainInternal["action"];
        }
    }

    /**
     * @param $type
     * @return bool
     */
    public function isAllowedParameter($type, $url)
    {
        foreach ($this->allowedTypesByUrl as $allowedUrl => $allowedTypes) {
            if (str_replace("http://", "", $url) == $allowedUrl) {
                foreach ($allowedTypes as $item) {
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
            if ($url == $allowedUrl) {
                foreach ($allowedTypes as $item) {
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
