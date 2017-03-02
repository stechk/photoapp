<?php

namespace App\Presenters;


use App\Model\ApiService;
use Model\PhotoModel;
use Nette;


class ApiPresenter extends BasePresenter
{

    const
        CODE_OK = 0,
        CODE_KEY_FAIL = 1,
        CODE_FAIL = 2;


    /**
     * Klič k api
     * @persistent
     */
    public $key;

    /**
     * @var ApiService
     * @inject
     */
    public $apiService;

    public function actionAuth($email, $password)
    {
        try {
            $this->user->login($email, $password);
            $user = $this->user->getIdentity();
            $this->sendJson(["data" => ["id" => $user->id, "name" => $user->full_name], "code" => self::CODE_OK]);
        } catch (Nette\Security\AuthenticationException $e) {
            $this->sendJson(["error" => "User or Password are wrong", "code" => self::CODE_FAIL]);
        }
        $this->terminate();

    }

    public function actionDomainCategories()
    {
        try {
            $categories = $this->apiService->findCategories($this->presenter->getHttpRequest()->getUrl()->host);
        } catch (\Exception $e) {
            $this->sendJson(["error" => "Categories canot be loaded.", "code" => self::CODE_FAIL]);
        }

        $this->sendJson(["data" => $categories, "code" => self::CODE_OK]);
    }

    public function actionAllCategories()
    {
        try {
            $categories = $this->apiService->findAllCategories();
        } catch (\Exception $e) {
            $this->sendJson(["error" => "Categories canot be loaded.", "code" => self::CODE_FAIL]);
        }
        $this->sendJson(["data" => $categories, "code" => self::CODE_OK]);
    }

    public function actionOp($op)
    {
        try {
            $opData = $this->apiService->findOp($op);
        } catch (\Exception $e) {
            $this->sendJson(["error" => "OP items not found.", "code" => self::CODE_FAIL]);
        }

        $this->sendJson(["data" => $opData, "code" => self::CODE_OK]);
    }

    public function actionImages($op)
    {
        try {
            $data = $this->apiService->findImages($op);
        } catch (\Exception $e) {
            $this->sendJson(["error" => "OP items not found.", "code" => self::CODE_FAIL]);
        }

        $this->sendJson(["data" => $data, "code" => self::CODE_OK]);
    }

    public function actionUpload()
    {
        try {
            $this->apiService->uploadImage($this->presenter->getHttpRequest(), $this->getContext()->parameters["wwwDir"] . '/files');
        } catch (\Exception $e) {
            $this->sendJson(["error" => "Upload failed.", "code" => self::CODE_FAIL]);
        }

        $this->sendJson(["data" => "", "code" => self::CODE_OK]);


    }

    protected function startup()
    {
        parent::startup();
        if ($this->apiService->verifyKey($this->key) === false) {
            $this->sendJson(["error" => "Key is not valid", "code" => self::CODE_KEY_FAIL]);
        }
    }

}
