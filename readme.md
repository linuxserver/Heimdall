![alt text](https://i.imgur.com/aUpMns5.jpg)

## About 

An Application dashboard and launcher

## Video
If you want to see a quick video of it in use, go to https://drive.google.com/file/d/1cijXgmjem_q2OfKMp36qVuXRiyOzvhWC/view

## Web Server Configuration

### Apache
A .htaccess file ships with the app, however, if it does not work with your Apache installation, try this alternative:

```
Options +FollowSymLinks
RewriteEngine On

RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^ index.php [L]
```

### Nginx
If you are using Nginx, the following directive in your site configuration will direct all requests to the index.php front controller:

```
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

## License

This app is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
