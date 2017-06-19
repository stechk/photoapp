<?php

namespace App\Presenters;


use Model\PhotoModel;
use Model\UsersModel;
use Nette;


class SignPresenter extends BasePresenter
{


    private $photoModel;
    private $userModel;


    public function __construct(PhotoModel $photoModel, UsersModel $usersModel)
    {
        $this->photoModel = $photoModel;
        $this->userModel = $usersModel;
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

    protected function createComponentSignUpForm()
    {
        $form = new Nette\Application\UI\Form();
        $form->addText('name', 'Křestní jméno:')
            ->setRequired('Please enter your name.');
        $form->addEmail('email', 'Email:')
            ->setRequired('Please enter your name.');
        $form->addText('lastname', 'Příjmení:')
            ->setRequired('Please enter your lastname.');

        $form->addPassword('password', 'Heslo:')
            ->setRequired('Please enter your password.');

        $form->addSubmit('send', 'Registrovat');

        $form->onSuccess[] = array($this, 'signUpFormSubmitted');
        return $form;

    }

    public function signUpFormSubmitted(Nette\Application\UI\Form $form, $values)
    {
        $values->password = $this->userModel->calculateHash($values->password);
       $this->userModel->registrate($values);
       $this->flashMessage('Registrace proběhla v pořádku');
       $this->redirect('Sign:in');
    }

}
