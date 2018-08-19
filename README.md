This project runs under a classical lampp/xampp stack installation.

Alternatively use Apachev2+ with PHP v7+ and ModRewrite module installed and enabled:

edit vhost file in order to enable the override rule

DocumentRoot "/opt/lampp/htdocs"
<Directory "/opt/lampp/htdocs">
AllowOverride All
Require all granted
</Directory>

extract the repository content directly under server document root 

then access to the management API using the base URL

{server_base_url}/conceptAPI/{entity}

You may find a more detailed API documentation here:
https://documenter.getpostman.com/view/2040373/RWToRJWU#18407ae8-d041-d1ee-d3d1-c30295b16df3
