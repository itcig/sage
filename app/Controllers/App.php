<?php

namespace App\Controllers;

use Cig\Sage\Controller\Debugger;
use Cig\Sage\Controller\Controller;
use Timber;

class App extends Controller {

	public function siteName() {
		return get_bloginfo('name');
	}

	public function nav($menu_name = 'primary') {
		return (new Components\Nav($menu_name))->get();
	}

	public function sidebar() {
		return Components\Sidebar::widgets();
	}

	public function title() {
		if (is_home()) {
			if ($home = get_option('page_for_posts', true)) {
				return get_the_title($home);
			}
			return __('App Latest Posts', 'sage');
		}
		if (is_archive()) {
			return get_the_archive_title();
		}
		if (is_search()) {
			return sprintf(__('Search Results for %s', 'sage'), get_search_query());
		}
		if (is_404()) {
			return __('Not Found', 'sage');
		}
		return get_the_title();
	}

	public static function asset($asset = '') {
		return asset_path($asset);
	}
}
