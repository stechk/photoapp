<?php

namespace App\Presenters;


use Model\PhotoModel;
use Nette;


class SignPresenter extends BasePresenter
{


    public $photoModel;


    public function __construct(PhotoModel $photoModel)
    {
        $this->photoModel = $photoModel;
    }

    public function beforeRender()
    {
        if ($this->user->isLoggedIn()) {
            $this->presenter->redirect('Homepage:default');
        }
    }

    protected function createComponentSignInForm()
    {
        $form = new Nette\Application\UI\Form();
        $form->addText('username', 'Email:')
            ->setRequired('Please enter your username.');

        $form->addPassword('password', 'Heslo:')
            ->setRequired('Please enter your password.');

        $form->addSubmit('send', 'Přihlásit se');

        $form->onSuccess[] = array($this, 'signInFormSubmitted');
        return $form;

    }

    public function signInFormSubmitted(Nette\Application\UI\Form $form, $values)
    {
        try {
            $this->user->login($values->username, $values->password);
        } catch (Nette\Security\AuthenticationException $e) {
            $form->addError($e->getMessage());
        }

    }

    public function actionOut()
    {
        $this->getUser()->logout();
        $this->flashMessage('Byli jste odhlášeni');
        $this->redirect('in');
    }

}
