<?php

namespace App\Presenters;


use Model\PhotoModel;
use App\Model\SoapService;
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

    public function __construct(PhotoModel $photoModel, SoapService $soapService)
    {
        $this->photoModel = $photoModel;
        $this->soapService = $soapService;
    }

    protected function beforeRender()
    {

        if (!$this->presenter->user->isLoggedIn()) {
            $this->flashMessage("Nemáte oprávnění", "alert-danger");
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
        $this->template->op = $this->op;


        if (count($post) > 0 && isset($post["_do"]) && $post["_do"] == "uploadForm-submit") {
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
                        'timestamp' => new Nette\Utils\DateTime()
                    ];
                    $this->photoModel->saveImage($saveData);
                }
            }
            $this->terminate();
        }
    }

    public function renderPhotoform()
    {
        $this->template->photos = $this->photoModel->findPhotoByOp($this->op)->fetchAssoc('type|formatted_date|id');
        $this->template->countUploadedPhotos = $this->getParameter('count');
        $this->template->allTypes = $this->photoModel->getAllTypes();
    }
    public function renderDefault(){
        $this->template->typesByUrl = $this->photoModel->getTypesByDomain($this->presenter->getHttpRequest()->getUrl()->host);
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
        $result = $this->soapService->GetCislaOP($values['op']);
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
        $form->addSubmit('submit', 'Ukončit focení')->onClick[] = [$this, 'uploadFormSubmitted'];
        return $form;
    }

    public function uploadFormSubmitted(Nette\Forms\Controls\SubmitButton $button)
    {
        $this->redirect('Homepage:'.$this->photoModel->getDomainAction($this->presenter->getHttpRequest()->getUrl()->host), array('type' => null, 'op' => null));
    }
}
