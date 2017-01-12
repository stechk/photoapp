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
        $router[] = new Route('', 'Sign:in');
        $router[] = new Route('odhlaseni/', 'Sign:out');
        $router[] = new Route('upload/[/<id>]', 'Homepage:photoform');
        $router[] = new Route('foto/<action>[/<id>]', [
            'presenter' => 'Homepage',
            'action' => self::getAction($domainInternal, $domainExternal),
        ]);
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
