<?php

namespace App;

use Timber;
use Roots\Sage\Container;
use Roots\Sage\Assets\JsonManifest;

/**
 * Theme assets
 */
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('sage/main.css', asset_path('styles/main.css'), false, null);
    wp_enqueue_script('sage/main.js', asset_path('scripts/main.js'), ['jquery'], null, true);
}, 100);

/**
 * Theme setup
 */
add_action('after_setup_theme', function () {
    /**
     * Enable features from Soil when plugin is activated
     * @link https://roots.io/plugins/soil/
     */
    add_theme_support('soil-clean-up');
    add_theme_support('soil-jquery-cdn');
    add_theme_support('soil-nav-walker');
    add_theme_support('soil-nice-search');
    add_theme_support('soil-relative-urls');

    /**
     * Enable plugins to manage the document title
     * @link https://developer.wordpress.org/reference/functions/add_theme_support/#title-tag
     */
    add_theme_support('title-tag');

    /**
     * Register navigation menus
     * @link https://developer.wordpress.org/reference/functions/register_nav_menus/
     */
    /*register_nav_menus([
        'primary_navigation' => __('Primary Navigation', 'sage')
    ]);*/

    /**
     * Enable post thumbnails
     * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
     */
    add_theme_support('post-thumbnails');

    /**
     * Enable HTML5 markup support
     * @link https://developer.wordpress.org/reference/functions/add_theme_support/#html5
     */
    add_theme_support('html5', ['caption', 'comment-form', 'comment-list', 'gallery', 'search-form']);

    /**
     * Enable selective refresh for widgets in customizer
     * @link https://developer.wordpress.org/themes/advanced-topics/customizer-api/#theme-support-in-sidebars
     */
    //add_theme_support('customize-selective-refresh-widgets');

    /**
     * Use main stylesheet for visual editor
     * @see resources/assets/styles/layouts/_tinymce.scss
     */
    add_editor_style(asset_path('styles/main.css'));
}, 20);

/**
 * Register sidebars
 */
add_action('widgets_init', function () {
    $config = [
        'before_widget' => '<section class="widget %1$s %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3>',
        'after_title'   => '</h3>'
    ];
    register_sidebar([
        'name'          => __('Primary', 'sage'),
        'id'            => 'sidebar-primary'
    ] + $config);
    register_sidebar([
        'name'          => __('Footer', 'sage'),
        'id'            => 'sidebar-footer'
    ] + $config);
});

/**
 * Updates the `$post` variable on each iteration of the loop.
 * Note: updated value is only available for subsequently loaded views, such as partials
 */
//add_action('the_post', function ($post) {
//    sage('blade')->share('post', $post);
//});

/**
 * Dump debug container into Javascript console if on development environment
 */
add_action('wp_footer', function() {
	if (WP_ENV === 'development') {
		echo '<script>console.log("***** THEME DEBUG *****");console.log(' . json_encode(sage('debug')->all(), JSON_PRETTY_PRINT) . ');console.log("***** END DEBUG *****");</script>';
	}
});

/**
 * Setup Sage options
 */
add_action('after_setup_theme', function () {
    /**
     * Add JsonManifest to Sage container
     */
    sage()->singleton('sage.assets', function () {
        return new JsonManifest(config('assets.manifest'), config('assets.uri'));
    });

	/**
	 * Add the directory of templates in include path
	 */
	$views_dir = config('view.folders');

	if (is_array(Timber::$dirname)) {
		Timber::$dirname += $views_dir;
	} elseif (Timber::$dirname) {
		Timber::$dirname = array_merge([Timber::$dirname], $views_dir);
	} else {
		Timber::$dirname = $views_dir;
	}

    /**
     * Add Timber to Sage container
     */
    sage()->singleton('sage.timber', function (Container $app) {
		return Timber;
    });
});

/*
 * Recommended action by Hierarchy to include subfolders, but calls theme template_include
 * multiple times
 */
/*add_action('template_redirect', function () {
	$finder = new \Brain\Hierarchy\Finder\SubfolderTemplateFinder(
		array_merge(config('view.folders'),
			[
				get_stylesheet_directory(),
				get_template_directory(),
			]),
		[ 'twig', 'php' ]);

	$queryTemplate = new \Brain\Hierarchy\QueryTemplate($finder);

	echo $queryTemplate->loadTemplate();
	//exit();
});*/