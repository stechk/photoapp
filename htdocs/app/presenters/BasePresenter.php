<?php
/**
 * Created by PhpStorm.
 * User: pl
 * Date: 01.09.16
 * Time: 6:30
 */

namespace App\Presenters;


use Nette\Application\UI\Presenter;

class BasePresenter extends  Presenter
{

    /**
     * Vyresetuje data ve formulari
     */
    public function handleResetForm()
    {
        $s = $this->getSession("form");
        if ($s->offsetGet("files")) {
            unset($s->files);
        }
        if ($s->offsetGet("form")) {
            unset($s->form);
        }
        $this->redirect("Homepage:form", array("id" => null));
    }


}