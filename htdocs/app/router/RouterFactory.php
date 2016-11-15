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
//		$router[] = new Route('[/<id>]', 'Homepage:default');
//		$router[] = new Route('moje-podnety/<id>', 'Homepage:form');
//		$router[] = new Route('moje-statistiky[/<id>]', 'Homepage:stat');
//		$router[] = new Route('prihlaseni[/<id>]', 'Secure:in');
//		$router[] = new Route('odhlaseni[/<id>]', 'Secure:out');
//		$router[] = new Route('moje-podnety[/<id>]', 'Homepage:default');
		$router[] = new Route('<presenter>/<action>[/<id>]', 'Homepage:default');
		return $router;
	}

}
