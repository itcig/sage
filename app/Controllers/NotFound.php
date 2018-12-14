<?php

namespace App\Controllers;

use Cig\Sage\Controller\Controller;

class NotFound extends Controller {

	public function templates() {
		return ['404.twig'];
	}
}
