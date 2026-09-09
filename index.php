<?php

defined('DS') or define('DS', '/');

/**
 * Resolves the local file path an asset URL points to.
 *
 * Handles both the relative paths passed by the css()/js() helpers and the
 * absolute URLs Kirby passes when it auto-loads template assets (see #8).
 * Returns null for anything that is not a local file — external scripts,
 * protocol-relative URLs and URLs carrying a query string or fragment.
 */
function cachebusterPath($kirby, string $url)
{
	// A query or fragment means this is not a plain file path. Rewriting it
	// would split the URL at the last dot and destroy it.
	if (strpbrk($url, '?#') !== false) {
		return null;
	}

	if (Url::isAbsolute($url) === false) {
		return $url;
	}

	// Absolute URL: only ours. Comparing against the index URL keeps
	// subfolder installations working, which Url::path() would not.
	$indexUrl = rtrim((string)$kirby->url('index'), '/');

	if ($indexUrl !== '' && str_starts_with($url, $indexUrl . '/')) {
		return ltrim(substr($url, strlen($indexUrl)), '/');
	}

	return null;
}

/**
 * Adds the file's modification timestamp to the filename so browsers reload
 * changed assets. Anything that is not a local, existing file is returned
 * untouched.
 */
function cachebusterUrl($kirby, string $url)
{
	if (!$kirby->option('schnti.cachebuster.active')) {
		return $url;
	}

	$path = cachebusterPath($kirby, $url);

	if ($path === null) {
		return $url;
	}

	$file = $kirby->roots()->index() . DS . $path;

	if (is_file($file) === false) {
		return $url;
	}

	// Insert the timestamp into the original URL so absolute URLs stay
	// absolute and relative ones stay relative.
	$dot = strrpos($url, '.');

	if ($dot === false) {
		return $url;
	}

	return substr($url, 0, $dot) . '.' . F::modified($file) . substr($url, $dot);
}

Kirby::plugin('schnti/cachebuster', [
	'options'    => [
		'active' => true
	],
	'components' => [
		'css' => function ($kirby, $url) {
			return cachebusterUrl($kirby, $url);
		},
		'js'  => function ($kirby, $url) {
			return cachebusterUrl($kirby, $url);
		}
	]
]);
