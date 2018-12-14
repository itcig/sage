<?php

namespace App;

use Roots\Sage\Container;
use Timber;

/**
 * Get the sage container which extends Illuminate\Container.
 *
 * @param string $abstract
 * @param array  $parameters
 * @param Container $container
 * @return Container|mixed
 */
function sage($abstract = null, $parameters = [], Container $container = null)
{
    $container = $container ?: Container::getInstance();
    if (!$abstract) {
        return $container;
    }
    return $container->bound($abstract)
        ? $container->makeWith($abstract, $parameters)
        : $container->makeWith("sage.{$abstract}", $parameters);
}

/**
 * Get / set the specified configuration value.
 *
 * If an array is passed as the key, we will assume you want to set an array of values.
 *
 * @param array|string $key
 * @param mixed $default
 * @return mixed|\Roots\Sage\Config
 * @copyright Taylor Otwell
 * @link https://github.com/laravel/framework/blob/c0970285/src/Illuminate/Foundation/helpers.php#L254-L265
 */
function config($key = null, $default = null)
{
    if (is_null($key)) {
        return sage('config');
    }
    if (is_array($key)) {
        return sage('config')->set($key);
    }
    return sage('config')->get($key, $default);
}

/**
 * Get / set the specified configuration value.
 *
 * If an array is passed as the key, we will assume you want to set an array of values.
 *
 * @param array|string $key
 * @param mixed $default
 * @param bool $single Key is a one-time set and will not be an array of debug data
 * @return mixed|\Roots\Sage\Config
 * @copyright Taylor Otwell
 * @link https://github.com/laravel/framework/blob/c0970285/src/Illuminate/Foundation/helpers.php#L254-L265
 */
function debug($key = null, $default = null, $single = false)
{
	if (is_null($key)) {
		return sage('debug');
	}

	if (is_array($key)) {
		return sage('config')->set($key);
	} else {
		if ($single) {
			sage('debug')->set($key, $default);
		} else {
			sage('debug')->push($key, $default);
		}
	}

	return sage('debug')->get($key, $default);
}


/**
 * @param string $file
 * @param array $data
 * @return string
 */
function template($file, $data = [])
{
	debug('event', 'Executing template() method');

	// Debug Wordpress variables and query objects
	global $wp, $wp_query;
	debug('query_vars', $wp->query_vars, true);
	debug('wp_query', $wp_query, true);

	// Debug all possible Twig template paths
	debug('template_paths', config('view.folders'));

	/**
	 * This came with sage. Why??
	 */
    if (remove_action('wp_head', 'wp_enqueue_scripts', 1)) {
        wp_enqueue_scripts();
    }

    $context = array_merge(Timber::get_context(), $data);

	$passed_template = str_replace(".php", ".twig", basename($file));
	$templates = [$passed_template, 'index.twig'];
	if (isset($context['templates'])) {
		if (is_array($context['templates'])) {
			$templates = array_merge($context['templates'], $templates);
		} else {
			$templates = array_unshift($templates, $context['templates']);
		}
	}

	// Debug all possible template being matched
	debug('event', 'Template Rendering');

	// Debug template match order
	debug ('template_order', $templates);

	// Add Twig $context to debug container
	debug('context', $context, true);

	return sage('timber')::compile($templates, $context);
	//return Timber::compile($templates, $context);
}

/**
 * Retrieve path to a compiled blade view
 * @param $file
 * @param array $data
 * @return string
 */
/*function template_path($file, $data = [])
{
    return sage('blade')->compiledPath($file, $data);
}*/

/**
 * @param $asset
 * @return string
 */
function asset_path($asset)
{
    return sage('assets')->getUri($asset);
}

/**
 * @param string|string[] $templates Possible template files
 * @return array
 */
function filter_templates($templates)
{
	$paths = apply_filters('sage/filter_templates/paths', config('view.folders'));

	$paths_pattern = "#^(" . implode('|', $paths) . ")/#";

	return collect($templates)
		->map(function ($template) use ($paths_pattern) {
			/** Remove .twig.php/.twig/.php from template names */
			$template = preg_replace('#\.(twig\.?)?(php)?$#', '', ltrim($template));

			/** Remove partial $paths from the beginning of template names */
			if (strpos($template, '/')) {
				$template = preg_replace($paths_pattern, '', $template);
			}

			return $template;
		})
		->flatMap(function ($template) use ($paths) {
			return collect($paths)
				->flatMap(function ($path) use ($template) {
					return [
						"{$path}/{$template}.php",  //breaks theme if removed
						//"{$template}.twig",  //needed?
						//"{$template}.php",  //needed?
					];
				});
		})
		->filter()
		->unique()
		->all();
}

/**
 * @param string|string[] $templates Relative path to possible template files
 * @return string Location of the template
 */
function locate_template($templates)
{
    return \locate_template(filter_templates($templates));
}

/**
 * Determine whether to show the sidebar
 * @return bool
 */
function display_sidebar()
{
    static $display;
    isset($display) || $display = apply_filters('sage/display_sidebar', false);
    return $display;
}
