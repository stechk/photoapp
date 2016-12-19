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
    public static function createRouter($domainInternal, $domainExternal)
    {
        $router = new RouteList;
        $router[] = new Route('foto[/<id>]', 'Homepage:photoform');
        $router[] = new Route('[<action>]', [
            'presenter' => 'Homepage',
            'action' => self::getAction($domainInternal,$domainExternal),
        ]);
        $router[] = new Route('<presenter>/<action>[/<id>]', 'Homepage:default');
        return $router;
    }

    private static function getAction($domain1,$domain2) {
        if ($_SERVER["HTTP_HOST"] == $domain1["domain"]) {
            return $domain1["action"];
        }elseif ($_SERVER["HTTP_HOST"] == $domain2["domain"]) {
            return $domain2["action"];
        }
        throw new Nette\NotImplementedException("Domena neni implementovana");
    }
}
