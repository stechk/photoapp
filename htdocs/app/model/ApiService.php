<?php
/**
 * Created by PhpStorm.
 * User: pl
 * Date: 03.09.16
 * Time: 10:45
 */

namespace App\Model;


use Model\PhotoModel;
use Model\UsersModel;
use Nette\Http\FileUpload;
use Nette\Http\Request;
use Nette\Utils\Arrays;
use Nette\Utils\DateTime;
use Nette\Utils\Json;

class ApiService
{

    private $apiKey;

    /**
     * @var PhotoModel
     */
    private $photoModel;

    /**
     * @var SoapService
     */
    private $soapService;

    /**
     * @var UsersModel
     */
    private $usersModel;

    /**
     * ApiService
     * @param $apiKey
     */
    public function __construct($apiKey, PhotoModel $photoModel, SoapService $soapService, UsersModel $usersModel)
    {
        $this->apiKey = $apiKey;
        $this->photoModel = $photoModel;
        $this->soapService = $soapService;
        $this->usersModel = $usersModel;
    }

    public function verifyKey($key)
    {
        if (empty($this->apiKey)) {
            throw new Exception("Api key not set");
        }
        if (trim($this->apiKey) != trim($key)) {
            return false;
        }
        return true;
    }


    public function verifyLogin($email, $password)
    {
        if (empty($email) || empty($password)) {
            return false;
        }

    }

    public function findCategories($domain) {
        $data = $this->photoModel->getTypesByDomain($domain);
        if (count($data) == 0 ) {
            throw new \Exception("There is no categories");
        }
        return $data;
    }

    public function findAllCategories() {
        $data = $this->photoModel->getAllTypes();
        if (count($data) == 0 ) {
            throw new \Exception("There is no categories");
        }
        return $data;
    }

    public function findOp($opString) {
        $data = $this->soapService->GetCislaOPPart($opString,"");
        if (count($data) == 0 ) {
            throw new \Exception("There is no results");
        }
        return $data;
    }

    public function findImages($op) {
        $photos = $this->photoModel->findPhotoByOp($op)->fetchAssoc('type|formatted_date|id');
        $users = $this->usersModel->getAllUsers()->fetchAssoc("users_id");
        foreach ($photos as $typ => $type){
            foreach ($type as $dat => $date){
                foreach ($date as $id => $data){
                    $photos[$typ][$dat][$id]["path"] = $_SERVER["REQUEST_SCHEME"]."://".$_SERVER["HTTP_HOST"].$data["filepath"];

                    if (!empty($data['user_id']) && isset($users[$data['user_id']])){
                        $photos[$typ][$dat][$id]['user'] = $users[$data['user_id']]["name"] ." ".$users[$data['user_id']]["lastname"];
                    } else {
                        $photos[$typ][$dat][$id]['user'] = "";
                    }
                    unset($photos[$typ][$dat][$id]["filepath"]);
                    unset($photos[$typ][$dat][$id]["file_name"]);
                    unset($photos[$typ][$dat][$id]["id"]);
                    unset($photos[$typ][$dat][$id]["formatted_date"]);
                    unset($photos[$typ][$dat][$id]["user_id"]);
                    unset($photos[$typ][$dat][$id]["op"]);
                }
            }
        }
        return $photos;
    }

    /**
     * @param Request $httpRequest
     * @param $filespath
     * @return bool
     * @throws \Exception
     */
    public function uploadImage(Request $httpRequest, $filespath) {
        $files = $httpRequest->getFiles();
        $post = $httpRequest->getPost();
        if (count($post) > 0) {
            if (count($files) > 0) {
                //ulozeni souboru z formulare
                /** @var FileUpload $file */
                $file = $files["upload"][0];
                $rand = rand(100, 999);
                $sharedPath = "/" . $this->op . '/' . $this->type . '/' . date('Y-m-d-H-i-') . time() . '-' . $rand . '-' . $file->getSanitizedName();
                $dest = $filespath . $sharedPath;
                $destView = "/files" . $sharedPath;

                if (isset($post['timestamp']) && $this->photoModel->validateDate($post['timestamp'])) {
                    $timeStamp = new DateTime($post["timestamp"]);
                } else {
                    $timeStamp = new DateTime();
                }
                if ($file->isOk()) {
                    $file->move($dest);
                    $saveData = [
                        'filepath' => $destView,
                        'file_name' => $file->getSanitizedName(),
                        'op' => $post["op"],
                        'type' => $post["category_id"],
                        'user_id' => $post["user_id"],
                        'timestamp' => $timeStamp,
                    ];
                    $this->photoModel->saveImage($saveData);
                    return true;
                }
                Throw new \Exception("File upload failed", 1);
            }
            Throw new \Exception("File upload failed", 2);
        }
        Throw new \Exception("File upload failed", 3);
    }
}
