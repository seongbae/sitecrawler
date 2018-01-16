Laravel Site Crawler
=======================

A Laravel package for crawling websites.  This is just a simple wrapper around the Goutte library where if you give it a URL, it wil crawl all the pages on that website, extract out title and meta description field and store them in database.  It also stores the entire HTML of each page for further analysis later on.

Installation
------------

Add the following line to the `require` section of your Laravel webapp's `composer.json` file:

```javascript
    "require": {
        "seongbae/sitecrawler": "1.*"
    }
```

Run `composer update` to install the package.

Or simpy run `composer require seongbae/sitecrawler`

This package uses Laravel 5.5 Package Auto-Discovery.

For previous versions of Laravel, you need to update `config/app.php` by adding an entry for the service provider:

```php
'providers' => [
    // ...
    seongbae\SiteCrawler\SiteCrawlerServiceProvider::class,
];
```

Next, publish all package resources:

```bash
    php artisan vendor:publish --provider="seongbae\SiteCrawler\SiteCrawlerServiceProvider"
```

This will add to your project:

    - migration - database tables for crawled content
    - configuration - package configurations

Remember to launch migration: 

```bash
    php artisan migrate
```


Usage
------

1) Crawl the entire website:

```php	
    $crawler = new SiteCrawler(Config::get('sitecrawler'));
    $crawler->crawl('www.lnidigital.com');
```

2) You can also run php artisan console command:

```php
    php artisan crawl www.lnidigital.com
```

You can add --nofollow as an optional parameter to crawl just a single page.

When migration is run, the package creates a table called "crawled_pages".  It's fields are:
```php
    id, url, scheme, host, path, title, description, html, status, created_at, updated_at
```

url - url of the page being crawled

scheme - protocol - "http://"" or "https://""

host - hostname of page being crawled.  i.e. "www.lnidigital.com"

path - path i.e. "/about"

title - title tag

description - description meta tag

html - entire HTML of page after being cleaned up by the Purifier library

status - status of page crawled - 200, 404, 500, etc

created_at - created time

updated_at - updated time


Changelog
---------

1.0
- Create package

Roadmap
-------
- Coming soon...

Credits
-------

This package is created by Seong Bae. 