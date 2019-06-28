# Clime

A work in progress, originally forked from [Do-it-yourself Weather Station](https://github.com/fractalxaos/weather). The notable difference is that the removal of python and switch to php. Also, rather than an 'agent' process, the db updates and other write functions are handled directly within the request made by the weather station.

## Requirements

- PHP 7.1 +
- Apache
- Ubuntu (for this example anyway)
- install rrdtool on (homebrew on OS X, apt-get on ubuntu '`sudo apt-get install librrds-perl rrdtool`)
- install [PHP rrd extension](http://php.net/manual/en/book.rrd.php) (PECL on OSX,  `sudo apt-get install php-rrd` on ubuntu)


### Ubuntu setup

Flash [ubuntu server](https://ubuntu.com/download/iot/raspberry-pi-2-3), to microSD using with [Etcher](https://www.balena.io/etcher/). Setup the Raspberry Pi and plug in SD card and connect it to power and an ethernet cable directly to router. Check the router's config page for the Pi's IP address after connecting.

Login to ubuntu replacing x.x.x.x with actual ip: `ssh ubuntu@x.x.x.x`—the default password is ubuntu—this must be changed on first login.

### Server setup

This is what I start almost all servers with in terms of apache config and php setup. The project does not immediately need rewrites, gd, curl, xml out of the box, but most projects eventually do want those features. I install git as a quick way to manage updates and deployments.

- `sudo apt-get update`
- `sudo apt-get install apache2`
- `sudo apt-get install php`
- `sudo apt-get install libapache2-mod-php`
- `sudo apt-get install php-xml`
- `sudo apt-get install curl`
- `sudo apt-get install php-curl`
- `sudo apt-get install php-gd`
- `sudo a2enmod rewrite`
- `sudo apachectl -k start` (if not running)
- `sudo apt-get install git`

### Configs

In php.ini, set the `date.timezone`,  `date.default_latitude` and `date.default_longitude`


### App Setup

Clone the [clime repository](https://github.com/brendanmetzger/clime), then point the DocumentRoot to `/var/www/**repo root**/public`. The `.conf` will be somewhere in `/etc/apache2/sites-enabled`, for reference. 

In your repo root,  `mkdir data` (or whatever you want—set that in config.ini) and set the owner to `sudo chown www-data data` (that's specific to ubuntu, it's `_www` on Mac OSX). Make another directory in your document root  (public by default), `mkdir public/charts` , and set the owner to `sudo chown www-data public/charts` as well.

---------






```
POST /post.php HTTP/1.1 
Host: http://weather.local 
Content-Type: multipart/form-data;boundary="boundary" 

--boundary 
Content-Disposition: form-data; name="windspeedmph" 

ws 

Connection: close


--boundary
Content-Disposition: form-data; name="winddir"

wd
--boundary
Content-Disposition: form-data; name="windgustmph"

gs
--boundary
Content-Disposition: form-data; name="windgustdir"

gd
--boundary
Content-Disposition: form-data; name="windspeedmph_avg2m"

ws2
--boundary
Content-Disposition: form-data; name="winddir_avg2m"

wd2
--boundary
Content-Disposition: form-data; name="windgustmph_10m"

gs10
--boundary
Content-Disposition: form-data; name="windgustdir_10m"

gd10
--boundary
Content-Disposition: form-data; name="humidity"

h
--boundary
Content-Disposition: form-data; name="tempf"

t
--boundary
Content-Disposition: form-data; name="rainin"

r
--boundary
Content-Disposition: form-data; name="dailyrainin"

dr
--boundary
Content-Disposition: form-data; name="pressure"

p
--boundary
Content-Disposition: form-data; name="batt_lvl"

b
--boundary
Content-Disposition: form-data; name="light_lvl"

l
```
