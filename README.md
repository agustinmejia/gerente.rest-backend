<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

# Gerente de restaurante

Plataforma web para administración de restaurantes y negocios de comida rápida.

## Requisitos
* php ^7.4, mysql ^8.0
* Instalar composer [Instrucciones](https://www.digitalocean.com/community/tutorials/how-to-install-and-use-composer-on-ubuntu-20-04-es).
* Instalar las extensiones de PHP requeridas en la documentación de laravel
```
sudo apt-get install php7.4-mbstring php7.4-intl php7.4-dom
```
* Instalar nodejs 14.X [instrucciones](https://computingforgeeks.com/install-node-js-14-on-ubuntu-debian-linux/)
```
curl -sL https://deb.nodesource.com/setup_14.x | sudo bash -
sudo apt -y install nodejs
```
* Instalar certbor para generar los certificados SSL, [instrucciones](https://www.digitalocean.com/community/tutorials/how-to-secure-nginx-with-let-s-encrypt-on-ubuntu-20-04-es)
```
sudo apt install certbot python3-certbot-nginx
sudo ufw allow 'Nginx Full'
```

## Instalación
```
composer install
cp .env.example .env
php artisan gerente:install
chmod -R 777 storage bootstrap/cache
sudo certbot --nginx -d example.com
```
Nota: Al ejecutar el comando de instalación (gerente:install), escribir "gerente.rest" como nombre del cliente de acceso personal.