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
    const TYPE_BRILIX = 'brilix';
    const TYPE_VYROBA = 'vyroba';

    private $domainInternal;
    private $domainExternal;

    /**
     * @var Connection
     */
    private $database;


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
            $this->domainInternal["domain"] => $this->domainInternal["sections"],
            $this->domainExternal["domain"] => $this->domainExternal["sections"]];
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

    public function getAllTypes(){
        foreach ($this->allowedTypesByUrl as $domainTypes) {
            foreach ($domainTypes as $type){
                $return[] = $type;
            }
        }
        return $return;
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

    public function getTypesByDomain($domain){
        foreach ($this->allowedTypesByUrl as $k => $domainTypes){
            if($k == $domain){
                $return = $domainTypes;
            }
        }
        return $return;
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
                                        FROM images as i WHERE i.op = ? ORDER BY formatted_date DESC', $op);
    }

    public function saveImage($data)
    {
        $this->database->query('INSERT INTO images', $data);
    }

}
