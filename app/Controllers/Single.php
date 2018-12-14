<?php

namespace App\Controllers;

use Cig\Sage\Controller\Controller;
use Timber;

class Single extends Controller {

	public function templates() {
		$post = get_post();

		if (post_password_required()) {
			return ['single-password.twig'];
		} else {
			return ['single-' . $post->ID . '.twig', 'single-' . $post->post_type . '.twig', 'single.twig'];
		}
	}

	public function post() {
		return Timber::query_post();
	}
}
