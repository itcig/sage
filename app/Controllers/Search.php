<?php

namespace App\Controllers;

use Cig\Sage\Controller\Controller;
use Timber;

class Search extends Controller {

	public function templates() {
		return ['search.twig', 'archive.twig'];
	}

	public function title() {
		$title = 'Search results for ' . get_search_query();

		return $title;
	}

	public function posts() {
		return Timber::get_posts();
	}

}