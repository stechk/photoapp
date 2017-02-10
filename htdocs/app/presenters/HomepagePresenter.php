<?php

namespace App\Presenters;


use Model\PhotoModel;
use App\Model\SoapService;
use Nette\Utils\DateTime;
use Model\UserAuthenticator;
use Nette;


class HomepagePresenter extends BasePresenter
{
    /**
     * Poznatek ID
     * @persistent
     */
    public $type;

    /**
     * OP
     * @persistent
     */
    public $op;


    public $photoModel;
    public $soapService;
    /**
     * @var UserAuthenticator
     */
    public $userAuthenticator;

    public function __construct(PhotoModel $photoModel, SoapService $soapService, UserAuthenticator $userAuthenticator)
    {

        $this->photoModel = $photoModel;
        $this->soapService = $soapService;
        $this->userAuthenticator = $userAuthenticator;
    }

    function validateDate($date)
    {
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }

    protected function beforeRender()
    {

        if (!$this->presenter->user->isLoggedIn()) {
            $this->presenter->redirect('Sign:in');
        }
        $files = $this->presenter->getHttpRequest()->getFiles();
        $post = $this->presenter->getHttpRequest()->getPost();
        $url = $this->presenter->getHttpRequest()->getUrl()->host;


        if (!$this->photoModel->isAllowedParameter($this->type, $url) && $this->type != null) {
            $this->type = null;
            $this->op = null;
            $this->redirect("Homepage:".$this->photoModel->getDomainAction($url));
        }
        $this->template->actionByUrl = $this->photoModel->getDomainAction($url);
        $this->template->type = $this->photoModel->getTypeByName($this->type, $url);
        $this->template->typesByUrl = $this->photoModel->getTypesByDomain($this->presenter->getHttpRequest()->getUrl()->host);
        $this->template->op = $this->op;

        if (count($post) > 0 && isset($post["_do"]) && $post["_do"] == "uploadForm-submit" && $this->validateDate($post['target_date'])) {
            if (count($files) > 0) {
                $filespath = $this->getContext()->parameters["wwwDir"] . '/files';
                //ulozeni souboru z formulare
                /** @var Nette\Http\FileUpload $file */

                $file = $files["upload"][0];
                $rand = rand(100, 999);
                $sharedPath = "/" . $this->op . '/' . $this->type . '/' . date('Y-m-d-H-i-') . time() . '-' . $rand . '-' . $file->getSanitizedName();
                $dest = $filespath . $sharedPath;
                $destView = "/files" . $sharedPath;

                if ($file->isOk()) {
                    $file->move($dest);
                    $saveData = [
                        'filepath' => $destView,
                        'file_name' => $file->getSanitizedName(),
                        'op' => $this->op,
                        'type' => $this->type,
                        'user_id' => $this->user->id,
                        'timestamp' => $post["target_date"]
                    ];
                    $this->photoModel->saveImage($saveData);
                }
            }
            $this->terminate();
        }
    }

    public function getAllUsersIdName(){
        $users=  $this->userAuthenticator->getAllUsers()->fetchAll();
        foreach ($users as $user){
            $return[$user['users_id']] = $user['name'].' '.$user['lastname'];
        }
        return $return;
    }

    public function renderPhotoform()
    {
        $this->template->opData = $this->soapService->GetCislaOPPart($this->op,'');
        $users = $this->getAllUsersIdName();
        $photos = $this->photoModel->findPhotoByOp($this->op)->fetchAssoc('type|formatted_date|id');
        foreach ($photos as $typ => $type){
            foreach ($type as $dat => $date){
                foreach ($date as $id => $data){

                        if (!empty($data['user_id']) && isset($users[$data['user_id']])){
                            $photos[$typ][$dat][$id]['user_full_name'] = $users[$data['user_id']];
                        }
                }
            }
        }
        $this->template->photos = $photos;
        $this->template->countUploadedPhotos = $this->getParameter('count');
        $this->template->allTypes = $this->photoModel->getAllTypes();
    }


    protected function createComponentSearchForm()
    {
        $form = new Nette\Application\UI\Form();
        $form->addText('op', 'Zadej číslo OP')
            ->addRule(Nette\Application\UI\Form::INTEGER, 'OP musí být číslo')
            ->setRequired('Zadejte OP');
        $form->addSubmit('search', 'Vyhledat');
        $form->onSuccess[] = [$this, 'searchFormSubmitted'];
        return $form;

    }

    public function searchFormSubmitted(Nette\Application\UI\Form $form, $values)
    {
        $result = $this->soapService->GetCislaOPPart($values['op'],'');
        if (!empty($result)) {
            $this->template->searchResult = $result;
        } else {
            $this->flashMessage('OP nebylo nalezeno');
            $this->redirect('this');
        }
    }

    protected function createComponentUploadForm()
    {
        $form = new Nette\Application\UI\Form();
        $form->addMultiUpload('upload')
            ->setRequired(FALSE)
            ->setAttribute('accept', 'image/*')
            ->addRule(Nette\Application\UI\Form::IMAGE, 'Formát jednoho nebo více obrázků není podporován.');
        $form->addText('target_date')
            ->setType('date')
            ->setRequired('Zadejte prosím datum')
            ->setDefaultValue(new Nette\Utils\DateTime());
        $form->addSubmit('submit', 'Ukončit focení')->onClick[] = [$this, 'uploadFormSubmitted'];
        return $form;
    }

    public function validateUploadForm($form,$values){
        dump($values);die;
    }

    public function uploadFormSubmitted(Nette\Forms\Controls\SubmitButton $button)
    {
        $this->redirect('Homepage:'.$this->photoModel->getDomainAction($this->presenter->getHttpRequest()->getUrl()->host), array('type' => null, 'op' => null));
    }
}
