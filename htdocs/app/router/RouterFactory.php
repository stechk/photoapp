<?php

namespace App;

use Nette;
use Nette\Application\Routers\RouteList;
use Nette\Application\Routers\Route;


class RouterFactory
{
    use Nette\StaticClass;

    //TODO zmenit url
    private static $urls = [
        'fotoapp.local' => [
            'action' => 'default'
        ],
        'fotoapp2.local' => [
            'action' => 'notDefault'
        ],
    ];

    /**
     * @return Nette\Application\IRouter
     */
    public static function createRouter()
    {
        $router = new RouteList;
        $router[] = new Route('foto[/<id>]', 'Homepage:photoform');
        $router[] = new Route('[<action>]', [
            'presenter' => 'Homepage',
            'action' => self::$urls[$_SERVER['HTTP_HOST']]['action'],
        ]);
        $router[] = new Route('<presenter>/<action>[/<id>]', 'Homepage:default');
        return $router;
    }

}
