<?php

namespace App\Controllers;

use Cig\Sage\Controller\Controller;
use Timber;

class Author extends Controller {

	public function templates() {
		return ['author.twig', 'archive.twig'];
	}

	public function title() {
		$author = get_queried_object();
		return $author->display_name ?? 'Author';
	}

	public function author() {
		return get_queried_object();
	}

	public function posts() {
		return Timber::query_posts();
	}
}
