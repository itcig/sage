<?php

namespace App\Controllers;

use Cig\Sage\Controller\Controller;
use Timber;

class Page extends Controller {

	public function templates() {
		$post = get_post();

		if (post_password_required()) {
			return ['single-password.twig'];
		} else {
			return ['page-' . $post->ID . '.twig', 'page.twig'];
		}
	}

	public function post() {
		return Timber::query_post();
	}
}
