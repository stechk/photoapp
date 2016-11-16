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
        $files = $this->presenter->getHttpRequest()->getFiles();
        $post = $this->presenter->getHttpRequest()->getPost();

        if (!$this->photoModel->isAllowedParameter($this->type) && $this->type != null) {
            $this->type = null;
            $this->op = null;
            $this->redirect("Homepage:default");
        }

        $this->template->type = $this->photoModel->getTypeByName($this->type);


        if (count($post) > 0 && isset($post["_do"]) && $post["_do"] == "uploadForm-submit") {
            if (count($files) > 0) {
                $filespath = $this->getContext()->parameters["wwwDir"] . '/files';
                //ulozeni souboru z formulare
                /** @var Nette\Http\FileUpload $file */
                $file = $files["upload"][0];
                $dest = $filespath . "/" . $this->op . '/' . $this->type . '/' . date('Y-m-d-H-i-') . $file->getSanitizedName();
                $destView = "/files" . "/" . $this->op . '/' . $this->type . '/' . date('Y-m-d-H-i-') . $file->getSanitizedName();
                $file->move($dest);
                $saveData = [
                    'filepath' => $destView,
                    'file_name' => $file->getSanitizedName(),
                    'op' => $this->op,
                    'type' => $this->type,
                    'timestamp' => new Nette\Utils\DateTime()
                ];
                $this->photoModel->saveImage($saveData);
            }
            $this->terminate();
        }
    }

    public function renderPhotoform()
    {
        $this->template->photos = $this->photoModel->findPhotoByOp($this->op)->fetchAll();
        $this->template->countUploadedPhotos = $this->getParameter('count');
    }

    protected function createComponentSearchForm()
    {
        $form = new Nette\Application\UI\Form();
        $form->addText('op', 'Hledej OP')
            ->addRule(Nette\Application\UI\Form::INTEGER, 'OP musí být číslo')
            ->setRequired('Zadejte OP');
        $form->addSubmit('search', 'Hledat');
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
        $form->addSubmit('submit', 'Ukončit')->onClick[] = [$this, 'uploadFormSubmitted'];
        return $form;
    }

    public function uploadFormSubmitted(Nette\Forms\Controls\SubmitButton $button)
    {
        $this->redirect('Homepage:default', array('type' => null, 'op' => null));
    }
}
