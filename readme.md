# Heimdall

[![Heimdall_Banner](https://i.imgur.com/iuV8w3y.png)](https://heimdall.site)

[![Discord](https://img.shields.io/discord/354974912613449730.svg)](https://discord.gg/CCjHKn4)
[![Docker Pulls](https://img.shields.io/docker/pulls/linuxserver/heimdall.svg)](https://hub.docker.com/r/linuxserver/heimdall/)
[![firsttimersonly](https://img.shields.io/badge/first--timers--only-friendly-blue.svg)](https://www.firsttimersonly.com/)
[![Paypal](https://heimdall.site/img/paypaldonate.svg)](https://www.paypal.me/heimdall)

___

Visit the website - https://heimdall.site
___

## About
As the name suggests Heimdall Application Dashboard is a dashboard for all your web applications. It doesn't need to be limited to applications though, you can add links to anything you like.

Heimdall is an elegant solution to organise all your web applications. It’s dedicated to this purpose so you won’t lose your links in a sea of bookmarks.

Why not use it as your browser start page? It even has the ability to include a search bar using either Google, Bing or DuckDuckGo.

![Heimdall demo animation](https://i.imgur.com/MrC4QpN.gif)

## Video
If you want to see a quick video of it in use, go to https://youtu.be/GXnnMAxPzMc

## Supported applications
You can use the app to link to any site or application, but Foundation apps will auto fill in the icon for the app and supply a default color for the tile. In addition Enhanced apps allow you provide details to an apps API, allowing you to view live stats directly on the dashboad. For example, the NZBGet and Sabnzbd Enhanced apps will display the queue size and download speed while something is downloading.

Supported applications are recognized by the title of the application as entered in the title field when adding an application. For example, to add a link to pfSense, begin by typing "p" in the title field and then select "pfSense" from the list of supported applications.

[![enhancedapps](https://img.shields.io/badge/dynamic/json.svg?label=Enhanced%20Apps&url=https%3A%2F%2Fapps.heimdall.site%2Fstats&query=enhanced_apps&colorB=3f8483&style=for-the-badge&logo=data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABwAAAAjCAMAAACw/5reAAAAnFBMVEUAAADu7u7u7u7u7u7u7u7x8fHu7u7u7u7u7u7u7u7u7u7u7u7r6+vu7u7v7+/u7u7t7e3v7+/v7+/u7u7u7u7u7u7u7u7u7u7u7u7u7u7v7+/u7u7p6ent7e3v7+/v7+/v7+/u7u7u7u7u7u7u7u7t7e3////u7u7u7u7u7u7u7u7w8PDw8PDt7e3u7u7t7e3s7Ozu7u7t7e3u7u4TnCP6AAAAM3RSTlMA+9n3phHw3czC088M5Y5zG6mflWdJFumyfj4sB2NeTi7hiWlDOQPGt5lsMiG9hFQntpFqxQJtAAABnElEQVQoz2WRh3KrQAxFtYWO6ZhucItrynv6/3/LFnA24c6wurpnYBkJZvXduNix6+GXTo8qWnxUPU4m2w0O1ktTozPsftiZpejGlm7C2MWUnRcWOohIo36+PaKyDZdLUOgDXvqQfaT9kwkfvP3AN18E7Kl8hkJHMHSXSSadxaTtTNjJhMkfjFHKMqGlolg4T7mtCbcq8gBCotxkwklFLIQSlQoTHnVWQqzNxYQuzpfmqGVMc5ijHK5yAuIhxbZ5p/S92RZkjv5BKs6aosSIr0JrcXBo1FtICVINKRKK6u0GnraoN84O5KbhjRwYzxCJnQCMtotkdNxjq2F7dJ2RoGuXIBTvc3ROthdmat6hZ7cOyfcxKGV+wTxBkxQxTQTzWOFny/7qS2nzx37T7nbtZj9xu7zUr/323nVy0sQnhwMJktSZrl5v7CjgSQmWi+haUCY8sH4tyc/FGSKGouS+WqBJm8U2NIE/+nLu2tzpF/xVNGy02QzRClafC/ysVpDzQJuA8xXsKl8bv+pgpXz57H9Yy3J1lQNY62wUrW+mdzrylWS0QwAAAABJRU5ErkJggg==)](https://apps.heimdall.site/applications/enhanced)

[![foundationapps](https://img.shields.io/badge/dynamic/json.svg?label=Foundation%20Apps&url=https%3A%2F%2Fapps.heimdall.site%2Fstats&query=foundation_apps&colorB=3f8483&style=for-the-badge&logo=data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABwAAAAjCAMAAACw/5reAAAAnFBMVEUAAADu7u7u7u7u7u7u7u7x8fHu7u7u7u7u7u7u7u7u7u7u7u7r6+vu7u7v7+/u7u7t7e3v7+/v7+/u7u7u7u7u7u7u7u7u7u7u7u7u7u7v7+/u7u7p6ent7e3v7+/v7+/v7+/u7u7u7u7u7u7u7u7t7e3////u7u7u7u7u7u7u7u7w8PDw8PDt7e3u7u7t7e3s7Ozu7u7t7e3u7u4TnCP6AAAAM3RSTlMA+9n3phHw3czC088M5Y5zG6mflWdJFumyfj4sB2NeTi7hiWlDOQPGt5lsMiG9hFQntpFqxQJtAAABnElEQVQoz2WRh3KrQAxFtYWO6ZhucItrynv6/3/LFnA24c6wurpnYBkJZvXduNix6+GXTo8qWnxUPU4m2w0O1ktTozPsftiZpejGlm7C2MWUnRcWOohIo36+PaKyDZdLUOgDXvqQfaT9kwkfvP3AN18E7Kl8hkJHMHSXSSadxaTtTNjJhMkfjFHKMqGlolg4T7mtCbcq8gBCotxkwklFLIQSlQoTHnVWQqzNxYQuzpfmqGVMc5ijHK5yAuIhxbZ5p/S92RZkjv5BKs6aosSIr0JrcXBo1FtICVINKRKK6u0GnraoN84O5KbhjRwYzxCJnQCMtotkdNxjq2F7dJ2RoGuXIBTvc3ROthdmat6hZ7cOyfcxKGV+wTxBkxQxTQTzWOFny/7qS2nzx37T7nbtZj9xu7zUr/323nVy0sQnhwMJktSZrl5v7CjgSQmWi+haUCY8sH4tyc/FGSKGouS+WqBJm8U2NIE/+nLu2tzpF/xVNGy02QzRClafC/ysVpDzQJuA8xXsKl8bv+pgpXz57H9Yy3J1lQNY62wUrW+mdzrylWS0QwAAAABJRU5ErkJggg==)](https://apps.heimdall.site/applications/foundation)

## Installing
Apart from the Laravel 8 dependencies, namely PHP >= 7.4.32, BCMath PHP Extension, INTL PHP Extension, Ctype PHP Extension, Fileinfo PHP extension, JSON PHP Extension, Mbstring PHP Extension, OpenSSL PHP Extension, PDO PHP Extension, Tokenizer PHP Extension, XML PHP Extension, the only other thing Heimdall needs is sqlite support and zip support (php-zip).

If you find you can't change the background make sure `php_fileinfo` is enabled in your php.ini. I believe it should be by default, but one user came across the issue on a windows system.

Installation is as simple as cloning the repository somewhere, or downloading and extracting the zip/tar and pointing your httpd document root to the `/public` folder then creating the .env file and generating an encryption key (this is all taken care of for you with the docker). 

```
cd /path/to/heimdall
cp .env.example .env
php artisan key:generate
```

For simple testing you could just go to the folder and type `php artisan serve`

There is also a multi-arch Docker which supports x86-64, armhf and arm64, instructions on how to use them at

- https://hub.docker.com/r/linuxserver/heimdall/

## Updating
To update your instance, simply clone this repository or download the zip/tar file with the new version and copy it over the old installation.

## Search Providers
v2.3.0 added the ability for users to customise the search options.

Options are stored in `/storage/app/searchproviders.yaml` (`/config/www/searchproviders.yaml` on docker installs), feel free to rearrange the options, add new ones, delete ones you don't use, etc.

Consider contributing to https://github.com/linuxserver/Heimdall/discussions/categories/search-providers to help others add new ones.

The item at the top of the list `Tiles` allows you to search for apps on your dashboard by name, helpful when you have lots of icons.

## New background image not being set
If you are using the docker image or a default php install you may find images over 2MB wont get set as the background image, you just need to change the `upload_max_filesize` in the php.ini.

If you are using the linuxserver.io docker image simply edit `/path/to/config/php/php-local.ini` and add `upload_max_filesize = 30M` to the end.

## Docker and enhanced apps
If you are running the docker and the EnhancedApps you are using are also in dockers, you may need to use the docker networking addresses to communicate with them.

You can do this by using `http(s)://docker_name:port` in the config section. Instead of the name you can use the internal docker ip, this usually starts with `172.`

## Languages
The app has been translated into several languages; however, the quality of the translations could do with work. If you would like to improve them, or help with other translations, they are stored in `/resources/lang/`.

To create a new language translation, make a new folder with the ISO 3166-1 alpha-2 code as the name, copy `app.php` from `/resources/lang/en/app.php` into your new folder and replace the English strings.

When you are finished, create a pull request.

Currently added languages are

- Breton
- Chinese
- Danish
- Dutch
- English
- Finnish
- French
- German
- Greek
- Hungarian
- Italian
- Japanese
- Korean
- Lombard
- Norwegian
- Polish
- Portuguese
- Russian
- Slovenian
- Spanish
- Swedish
- Turkish

## Web Server Configuration

### Apache
A `.htaccess` file ships with the app, however, a lot of apache installations disallow `.htaccess` files by default.
You will notice this due to some links not working like `/settings`.
In addition mod-rewrite needs to be enabled if it isn't already.

#### Fixes & work around options
##### - Apache global allow .htaccess
Find the `AllowOverride None` line in your apache configuration and change this to `AllowOverride All`

##### - Apache vhost configuration allow .htaccess
In the apache vhost configuration in the `<Directory />` block add `AllowOverride All`

##### - Add .htaccess content in apache configuration
You can add the full `.htaccess` into your apache configuration, this way you do not need to allow `.htaccess` files.
You can even shorten the content of the `.htaccess` when inserting it into the apache configuration to:
```
Options +FollowSymLinks
RewriteEngine On

RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^ index.php [L]
```
#### More info
More info about `AllowOverride` can be found here:
https://httpd.apache.org/docs/2.4/mod/core.html#allowoverride



### Nginx
If you are using Nginx, the following directive in your site configuration will direct all requests to the `index.php` front controller:

```
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```
Someone was using the same nginx setup to both run this and reverse proxy Plex, Plex is served from `/web` so their location was interfering with the `/webfonts`.

Therefore, if your fonts aren't showing because you have a location for `/web`, add the following
```
location /webfonts {
    try_files $uri $uri/;
}
```
If there are any other locations which might interfere with any of the folders in the `/public` folder, you might have to do the same for those as well, but it's a super fringe case.

### Reverse proxy
If you'd like to reverse proxy this app, we recommend using our letsencrypt/nginx docker image: [SWAG - Secure Web Application Gateway](https://hub.docker.com/r/linuxserver/swag)
You can either reverse proxy from the root location, or from a subdomain (subfolder method is currently not supported). For HTTPS proxy, make sure you use the HTTPS port of Heimdall webserver, otherwise some links may break. You can add security through `.htpasswd`

```
location / {
    auth_basic "Restricted";
    auth_basic_user_file /config/nginx/.htpasswd;
    include /config/nginx/proxy.conf;
    proxy_set_header X-Forwarded-Proto https;
    proxy_pass http://heimdall;
}
```

### Self-signed certificates and local CAs
Per default Heimdall uses the standard certificate bundle file (`ca-certificates.crt`) to verify HTTPS sites and will ignore additional certificates placed in `/etc/ssl/certs`. If you wish to use enhanced apps with HTTPS sites that use a self-signed certificate or certs signed with your own local CA, you can override the default bundle:

- Create a unified certificate `.pem` file that contains all CAs and certificates that Heimdall has to verify. For example, if you use both LetsEncrypt and a local CA for your internal apps, concatenate the LetsEncrypt intermediate CA (export via browser) and your local CA `cert.pem` (or any number of self-signed certs) into one `heimdall.pem` file.
- Place the `heimdall.pem` into the container (if you use Docker), for example by placing it in the path that you mapped to `/config`. Make sure that the Heimdall user has read access (`chmod a+r`).
- Set the `openssl.cafile` setting in `/config/php/php-local.ini` to your cert bundle:

```
# /config/php/php-local.ini
openssl.cafile = /config/heimdall.pem
```

Restart the container and the enhanced apps should now be able to access your local HTTP websites. This configuration will survive updating or recreating the Heimdall container.

## Running offline
The apps list is hosted on github, you have a couple of options if you want to run without a connection to the outside world:
1) Clone the repository and host it yourself, look at the .github actions file to see how to generate the apps list.
2) Download the apps list and store it as a json accessible to heimdall named `list.json`

With both options all you need to do is add the following to your `.env`
`APP_SOURCE=http://localhost/` Where `http://localhost/` is the path to the apps list without the name of the file, so if your file is stored at `https://heimdall.local/list.json` you would put `APP_SOURCE=https://heimdall.local/`

## Support
https://discord.gg/CCjHKn4 or through GitHub issues

## Donate
If you would like to show your appreciation, feel free to use the link below.

[![PayPal](https://heimdall.site/img/paypaldonate.svg)](https://www.paypal.me/heimdall)

## Credits
- PHP Framework - [Laravel](https://laravel.com/)
- Icons - [FontAwesome 5](https://fontawesome.com/)
- JavaScript - [jQuery](https://jquery.com/)
- Colour picker - [Huebee](http://huebee.buzz/)
- Background image - [pexels](https://www.pexels.com)
- Trianglify library - [Trianglify](https://github.com/qrohlf/trianglify)
- Everyone at Linuxserver.io that has helped with the app and let's not forget IronicBadger for the following question that started it all:
```
you know, i would love something like this landing page for all my servers apps
that gives me the ability to pin favourites
and / or search
@Stark @Kode do either of you think you'd be able to rustle something like this up ?
```

## License

This app is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
