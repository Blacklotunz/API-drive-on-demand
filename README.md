This project runs under a classical lampp/xampp stack installation.

Alternatively use Apachev2+ with PHP v7+ and ModRewrite module installed and enabled:

edit vhost file in order to enable the override rule

```
DocumentRoot "/opt/lampp/htdocs"
<Directory "/opt/lampp/htdocs">
AllowOverride All
Require all granted
</Directory>
```
extract the repository content directly under server document root 

then access to the management API using the base URL

{server_base_url}/conceptAPI/{entity}

You may find a more detailed API documentation here:
https://documenter.getpostman.com/view/2040373/RWToRJWU#18407ae8-d041-d1ee-d3d1-c30295b16df3

In order to execute the test suite phpunit is required.

To install it you need composer. (I didn't committed it on purpose) 

Since it's opensurce and easy to download, you may found a quick installation guide here: 
```
https://getcomposer.org/doc/00-intro.md
```

Then, once you have composer, open a shell, move into conceptReply_testSuite folder and type:

```
composer update
```

after phpunit and its dependecies are downloaded execute 

```
./vendor/bin/phpunit
```
