# Clime

A work in progress, originally forked from [Do-it-yourself Weather Station](https://github.com/fractalxaos/weather). The notable difference is that the removal of python and switch to php. Also, rather than an 'agent' process, the db updates and other write functions are handled directly within the request made by the weather station.

## Requirements

- PHP 7.1 +
- install rrdtool on (homebrew on OS X, apt-get on ubuntu)
- install [PHP rrd extension](http://php.net/manual/en/book.rrd.php) (PECL on OSX,  `apt-get install php-rrd` on ubuntu)
- This is using apache





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
