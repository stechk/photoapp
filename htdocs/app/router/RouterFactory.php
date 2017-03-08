<?php

namespace App;

use Nette;
use Nette\Application\Routers\RouteList;
use Nette\Application\Routers\Route;


class RouterFactory
{
    use Nette\StaticClass;

    /**
     * @return Nette\Application\IRouter
     */
    public static function createRouter()
    {
        $router = new RouteList;
        $router[] = new Route('', 'Sign:in');
        $router[] = new Route('odhlaseni/', 'Sign:out');
        $router[] = new Route('upload/[/<id>]', 'Homepage:photoform');
        $router[] = new Route('foto/<action>[/<id>]', [
            'presenter' => 'Homepage',
            'action' => 'default',
        ]);
        $router[] = new Route("api/<action>","Api:default");
        return $router;
    }
}
