<?php

namespace App\Controllers;

use Cig\Sage\Controller\Controller;

class Home extends Controller {

	public function templates() {
		return ['home.twig'];
	}
}
