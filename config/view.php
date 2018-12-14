<?php

/**
 * Search a directory for sub directories and return an array with the directory names
 *
 * @param string $directory_path The directory you wish to search
 * @param bool $search_recursive Defaults to true, but if false is passed will limit the search to the passed in directory only
 *
 * @return null|array Returns array with directory names ie ['dir_one', 'dir_two', 'dir_two/dir_three']
 */
function directory_list($directory_path, $search_recursive = true)
{
	if (file_exists($directory_path)) {
		foreach (array_diff(scandir($directory_path), ['.', '..']) as $directory) {
			if (is_dir($directory_path . '/' . $directory)) {
				$directory_list[] = pathinfo($directory, PATHINFO_FILENAME);
				if ($search_recursive) {
					$reucrsive_list = directory_list(($directory_path . '/' . $directory), $search_recursive);
					if (!empty($reucrsive_list)) {
						foreach ($reucrsive_list as $dir) {
							$directory_list[] = $directory . '/' . $dir;
						}
					}
				}
			}
		}
	}
	return $directory_list ?? null;
}

/**************************************************
 *** Dynamically build paths and directory names ***
 ***************************************************/
$paths = [];
$folders = [];

$theme_directories = [
	'/views',
	'/resources/views',
];

foreach ($theme_directories as $directory){

	/* Add Child and Parent theme directories to paths */
	$theme_directory_path = get_theme_file_path() . $directory;
	$parent_directpry_path = get_parent_theme_file_path() . $directory;

	if (!in_array($theme_directory_path, $paths)) {
		$paths[] = $theme_directory_path;
	}

	if (!in_array($parent_directpry_path, $paths)) {
		$paths[] = $parent_directpry_path;
	}

	/* Add child theme directories and sub directories to folders */
	$theme_directory_name = substr($directory, (strrpos($directory,'/') + 1));

	if (!in_array($theme_directory_name,$folders)) {
		$folders[] = $theme_directory_name;
	}

	$sub_directories = (directory_list($theme_directory_path) ?? []);

	foreach ($sub_directories as $sub_directory) {
		$sub_directory_name = $theme_directory_name . '/' . $sub_directory;
		if (!in_array($sub_directory_name,$folders)) {
			$folders[] = $sub_directory_name;
		}
	}
}




return [

    /*
    |--------------------------------------------------------------------------
    | View Storage Paths
    |--------------------------------------------------------------------------
    |
    | Most template systems load templates from disk. Here you may specify
    | an array of paths that should be checked for your views.
    |
    */

	'paths' => $paths,


    /*
    |--------------------------------------------------------------------------
    | Compiled View Path
    |--------------------------------------------------------------------------
    |
    | This option determines where all the compiled Blade templates will be
    | stored for your application. Typically, this is within the uploads
    | directory. However, as usual, you are free to change this value.
    |
    */

    'compiled' => wp_upload_dir()['basedir'].'/cache',

	/*
    |--------------------------------------------------------------------------
    | View Twig Folders
    |--------------------------------------------------------------------------
    |
    | Specify subfolder to load Twig templates from other than default theme resources/views
    |
    */
	'folders' => $folders,
];
