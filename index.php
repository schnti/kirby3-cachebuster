<?php

defined('DS') or define('DS', '/');

Kirby::plugin('schnti/cachebuster', [
	'options'    => [
		'active' => true
	],
	'components' => [
		'css' => function ($kirby, $url) {

			if ($kirby->option('schnti.cachebuster.active')) {
				$relative_url = Url::path($url, false);
				$file = $kirby->roots()->index() . DS . $relative_url;
				
				return dirname($relative_url) . '/' . F::name($relative_url) . '.' . F::modified($file) . '.css';
			} else {
				return $url;
			}
		},
		'js'  => function ($kirby, $url) {

			if ($kirby->option('schnti.cachebuster.active')) {
				$relative_url = Url::path($url, false);
				$file = $kirby->roots()->index() . DS . $relative_url;
				return dirname($relative_url) . '/' . F::name($relative_url) . '.' . F::modified($file) . '.js';

			} else {
				return $url;
			}
		}
	]
]);
