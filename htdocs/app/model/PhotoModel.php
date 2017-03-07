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

    private $domains;

    /**
     * @var Connection
     */
    private $database;


    /**
     * PhotoModel constructor.
     * @param Connection $database
     */
    public function __construct(Connection $database, $domains)
    {
        $this->database = $database;
        $this->domains = $domains;
    }

    public function getAllTypes(){
        foreach ($this->domains as $k => $allowedDomain){
            foreach ($allowedDomain['sections'] as $type){
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
        foreach ($this->domains as $allowedUrl => $data) {
            if (str_replace("http://", "", $url) == $allowedUrl) {
                foreach ($data['sections'] as $item) {
                    if ($item["id"] == $type) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public function getTypesByDomain($domain){
        foreach ($this->domains as $url => $data){
            if($url == $domain){
                $return = $data['sections'];
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
        foreach ($this->domains as $allowedUrl => $data) {
            if ($url == $allowedUrl) {
                foreach ($data['sections'] as $item) {
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

    public function validateDate($date)
    {
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }


}
