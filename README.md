# Hacker News

My cool HN clone


## Requirements

* Web server like Apache or Nginx

* PHP 7

* Composer

* MySQL / MariaDB

  

## Installation

1. Put this your web server.
2. Create a new MySQL database named `hackernews`.
3. Grab the SQL dump in the `/data` folder to populate the database.
4. Change the file `/config/db-options.yml-example` and rename it to `db-options.yml`.
5. Configure your server(https://silex.symfony.com/doc/2.0/web_servers.html) to redirect all requests to `index.php` in the webroot.



## Apache

If you are using Apache, use `mod_rewrite` with this `.htaccess` file:

```
<IfModule mod_rewrite.c>
    Options -MultiViews
    RewriteEngine On
    #RewriteBase /path/to/app
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [QSA,L]
</IfModule>
```