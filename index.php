<?php

defined('DS') or define('DS', '/');


function isLocalEnv () {
  return $_SERVER['REMOTE_ADDR'] === '127.0.0.1';
}


function isExternal ($url) {
  $link = parse_url($url);
  if (!isset($link['host'])) return false;

  $s = parse_url($_SERVER['HTTP_HOST']);
  // $host = parse_url($_SERVER['HTTP_HOST'])['host'];

  $host = isset($s['host'])
    ? $s['host']
    : $s['path'];

  if ($host === $link['host']) return false;
  
  return true;
}


function createHash ($kirby, $str, $ext) {
    // plugin inactive, skip
  if (!$kirby->option('schnti.cachebuster.active')) {
    return $str;
  }

  // local env detected, local hashes disabled, skip
  if (isLocalEnv() && !$kirby->option('schnti.cachebuster.local')) {
    return $str;
  }

  // link is external, skip
  if (isExternal($str)) return $str;

  // create hashed asset
  $u = parse_url($str);
  $url = $u['path'];

  $file = $kirby->roots()->index() . DS . $url;

  $filename = join([
    F::name($url),
    hash('crc32', F::modified($file)),
    $ext
  ], '.');

  $hashed = dirname($url) . '/' . $filename;
  
  return (isset($u['query']))
    ? join([$hashed, $u['query']], '?')
    : $hashed;
}


Kirby::plugin('schnti/cachebuster', [
  'options'    => [
    'active' => true,
    'local'  => false,
  ],
  'components' => [
    'css' => function ($kirby, $url) {
      return createHash($kirby, $url, 'css');
    },
    'js'  => function ($kirby, $url) {
      return createHash($kirby, $url, 'js');
    }
  ]
]);
