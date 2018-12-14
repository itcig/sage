<?php

namespace App\Controllers;

use Cig\Sage\Controller\Controller;

class Archive extends Controller {

	public function templates() {
		$templates = ['archive.twig'];

		if (is_category()) {
			array_unshift($templates, 'archive-' . get_query_var('cat') . '.twig');

		} else if (is_post_type_archive()) {
			array_unshift($templates, 'archive-' . get_post_type() . '.twig');
		}

		return $templates;
	}

	public function title() {
		$title = 'Archive';

		if (is_day()) {
			$title = 'Archive: ' . get_the_date('D M Y');

		} else if (is_month()) {
			$title = 'Archive: ' . get_the_date('M Y');

		} else if (is_year()) {
			$title = 'Archive: ' . get_the_date('Y');

		} else if (is_tag()) {
			$title = single_tag_title('', false);

		} else if (is_category()) {
			$title = single_cat_title('', false);

		} else if (is_post_type_archive()) {
			$title = post_type_archive_title('', false);
		}

		return $title;
	}

}
