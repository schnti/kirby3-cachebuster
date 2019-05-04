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
			    $file_root = $kirby->root('index') . DS . $relative_url;

			    if (F::exists($file_root)) {
				return url(dirname($relative_url) . '/' . F::name($relative_url) . '.' . F::modified($file_root) . '.css');
			    }
			}
			
		    	return $url;
		},
		'js'  => function ($kirby, $url) {
			
			if ($kirby->option('schnti.cachebuster.active')) {
				
			    $relative_url = Url::path($url, false);
			    $file_root = $kirby->root('index') . DS . $relative_url;

			    if (F::exists($file_root)) {
				return url(dirname($relative_url) . '/' . F::name($relative_url) . '.' . F::modified($file_root) . '.js');
			    }
			}
			
		    	return $url;
		}
	]
]);
