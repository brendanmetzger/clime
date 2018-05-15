# Clime

A work in progress, originally forked from [Do-it-yourself Weather Station](https://github.com/fractalxaos/weather). The notable difference is that the removal of python and switch to php. Also, rather than an 'agent' process, the db updates and other write functions are handled directly within the request made by the weather station.

## Requirements

- PHP 7.1 +
- install rrdtool on (homebrew on OS X, apt-get on ubuntu)
- install [PHP rrd extension](http://php.net/manual/en/book.rrd.php) (PECL on OSX,  `apt-get install php-rrd` on ubuntu)
- This is using apache