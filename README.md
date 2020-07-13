# Cachebuster plugin

This is a fork of [schnti/cachebuster](https://github.com/schnti/kirby3-cachebuster) plugin, with added functionality.


## Features

Differences to `schnti/cachebuster`

- Disable on local environment
- Detect and avoid modifying external links
- Preserve query string
- Hash instead of modified time


## Options

- `active` enable/disable plugin completely
- `local`  disable locally

You can control the plugin with the following line in your `/site/config/config.php`:

```php
return [
  'schnti.cachebuster.active' => true (default),
  'schnti.cachebuster.local' => false (default),
];
```

## How to use it

Add the following lines to your htaccess file:

```htaccess
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^(.+)\.(\w+)\.(js|css)$ $1.$3 [L]
```
