<?php

namespace App\Controllers\Components;

use Cig\Sage\Controller\Controller;
use Timber;

class Nav extends Controller {

	private $menu;

	public function __construct($menu_name = 'primary') {
		$this->menu = new Timber\Menu($menu_name);
	}

	public function get() {
		return $this->menu;
	}

}
