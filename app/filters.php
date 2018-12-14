<?php

namespace App;


use Cig;

/**
 * Add <body> classes
 */
add_filter('body_class', function (array $classes) {
    /** Add page slug if it doesn't exist */
    if (is_single() || is_page() && !is_front_page()) {
        if (!in_array(basename(get_permalink()), $classes)) {
            $classes[] = basename(get_permalink());
        }
    }

    /** Add class if sidebar is active */
    if (display_sidebar()) {
        $classes[] = 'sidebar-primary';
    }

    /** Clean up class names for custom templates */
    $classes = array_map(function ($class) {
        return preg_replace(['/-twig(-php)?$/', '/^page-template-views/'], '', $class);
    }, $classes);

    return array_filter($classes);
});

/**
 * Sidebar is on by default unless one of these conditions is met and returns false (to hide sidebar)
 */
add_filter('sage/display_sidebar', function() {
	return true;
});

/**
 * Add "… Continued" to the excerpt
 */
add_filter('excerpt_more', function () {
    return ' &hellip; <a href="' . get_permalink() . '">' . __('Continued', 'sage') . '</a>';
});

/**
 * Template Hierarchy should search for .twig files
 * This relies on Brain\Hierarchy template parser running all applicable filters from the array below
 * for the current WP_Query
 */
collect([
    'index', '404', 'archive', 'author', 'category', 'tag', 'taxonomy', 'date', 'home',
    'frontpage', 'page', 'paged', 'search', 'single', 'singular', 'attachment'
])->map(function ($type) {
    add_filter("{$type}_template_hierarchy", __NAMESPACE__.'\\filter_templates');
});

/**
 * Render page using Timber
 */
add_filter('template_include', function ($template) {
	debug('event', 'Including templates based on body_classes');

    $data = collect(get_body_class())->reduce(function ($data, $class) use ($template) {
	    debug('classes', $class);
        return apply_filters("sage/template/{$class}/data", $data, $template);
    }, []);

    if ($template) {
        echo template($template, $data);
        return get_stylesheet_directory().'/index.php';
    }

    return $template;
}, PHP_INT_MAX);

/**
 * Tell WordPress how to find the compiled path of comments.twig
 */
add_filter('comments_template', function ($comments_template) {
    $comments_template = str_replace(
        [get_stylesheet_directory(), get_template_directory()],
        '',
        $comments_template
    );
    return template_path(locate_template(["views/{$comments_template}", $comments_template]) ?: $comments_template);
});
