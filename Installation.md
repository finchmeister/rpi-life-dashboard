# Installation

##Install Raspbian

Use Etcher to burn Raspbian image to sd card.

Add an empty file `ssh` to the sd card to enable ssh.

##SSH into Pi

Install `nmap` then run:
```
nmap -sn 192.168.0.0/24
```
to find other devices on the network and obtain the ip of the pi.

SSH into Pi:
```
ssh pi@192.168.0.33
```

###Upgrade to Buster

PHP 7.1 is not supported on the stable release of Debian.
So upgrade to 'Buster' for the latest features.
Firstly, update the current version:
```
sudo apt-get update
sudo apt-get upgrade
sudo apt-get dist-upgrade
```
Then track the latest branch with:
```
sed -i 's/stretch/buster/g' /etc/apt/sources.list
```
Upgrade to Buster:
```
sudo apt-get update
sudo apt-get upgrade
sudo apt-get dist-upgrade
```

##Install PHP 7.1

This seems to install Apache too:
```
sudo apt-get install php7.1
```

##Restart Apache

```
sudo service apache2 restart
```

##Confirm Working
Create a test phpinfo file in the document root:
```php
<?php
// /var/www/html/info.php
phpinfo();
```

Visit:
<http://192.168.0.33/info.php>

##Update permissions and get reposiotry
```
sudo mkdir /var/www/rpi-life-dashboard
sudo chown -R pi /var/www/rpi-life-dashboard
cd /var/www/rpi-life-dashboard
git clone https://github.com/finchmeister/rpi-life-dashboard.git . 
```

##Composer
```
curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer
```
