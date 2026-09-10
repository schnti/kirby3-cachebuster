# Cachebuster plugin

A plugin for [Kirby 3 CMS](http://getkirby.com) to add modification timestamps to your css and js files, as long as they are embedded with the css() and js() helpers.

Please note that this plugin doesn't add caching headers to your CSS and JS files. To make proper use of this plugin, you need to add caching rules to your server configuration.

## What gets a timestamp – and what doesn't

Only local files that actually exist on disk are rewritten. Everything else is
returned unchanged:

| URL passed to `css()` / `js()` | Result |
| --- | --- |
| `assets/main.js` | `assets/main.<timestamp>.js` |
| `https://your-site.tld/assets/css/templates/blog.css` (Kirby's automatic template assets) | `…/blog.<timestamp>.css` |
| `https://cdn.example.com/lib.js` (external) | unchanged |
| `//cdn.example.com/lib.js` (protocol-relative) | unchanged |
| `https://maps.googleapis.com/maps/api/js?key=…` (query string) | unchanged |
| `assets/not-generated-yet.js` (file missing) | unchanged |

Absolute URLs of your own site are matched against `$kirby->url('index')`, so
subfolder installations work as well. The timestamp is inserted into the
original URL, which keeps absolute URLs absolute and relative ones relative.

## Commercial Usage

This plugin is free but if you use it in a commercial project please consider

- [making a donation](https://www.paypal.me/schnti/5) or
- [buying a Kirby license using this affiliate link](https://a.paddle.com/v2/click/1129/48194?link=1170)


## Installation

### Download

[Download the files](https://github.com/schnti/kirby3-cachebuster/archive/master.zip) and place them inside `site/plugins/cachebuster`.

### Composer

```
composer require schnti/cachebuster
```

### Git Submodule
You can add the plugin as a Git submodule.

    $ cd your/project/root
    $ git submodule add https://github.com/schnti/kirby3-cachebuster.git site/plugins/cachebuster
    $ git submodule update --init --recursive
    $ git commit -am "Add Kirby Cachebuster plugin"

Run these commands to update the plugin:

    $ cd your/project/root
    $ git submodule foreach git checkout master
    $ git submodule foreach git pull
    $ git commit -am "Update submodules"
    $ git submodule update --init --recursive

## Options

You can disable the plugin with the following line in your `/site/config/config.php`:

```php
return [
  'schnti.cachebuster.active' => true (default),
];
```


## How to use it

### htaccess rules for Apache

The timestamp is part of the filename, and that file does not exist on disk —
so the server has to rewrite `main.1234567890.css` back to `main.css`. On
Apache, add these lines to your htaccess file:

```apache
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^(.+)\.(\d+)\.(js|css)$ $1.$3 [L]
```

**The order matters.** The rule has to come *before* Kirby's catch-all,
otherwise the request is routed to `index.php` first and you get a 404:

```apache
RewriteEngine on
RewriteBase /

# cachebuster – before everything that routes to index.php
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^(.+)\.(\d+)\.(js|css)$ $1.$3 [L]

RewriteRule ^content/(.*) index.php [L]
RewriteRule ^site/(.*) index.php [L]

# Kirby's catch-all last
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*) index.php [L]
```

**Running Kirby from a public folder** changes nothing about the rules. Put
the `.htaccess` into the document root itself — the `public/` folder next to
`index.php` — and leave `RewriteBase /`, because that folder is served at `/`.
Only change `RewriteBase` if the site lives in a URL subfolder such as
`https://example.com/mysite`.

If nothing happens at all, check that `AllowOverride All` is enabled for the
directory; otherwise the `.htaccess` is ignored.


