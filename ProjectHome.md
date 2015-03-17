# [TorrentPier II](http://en.wikipedia.org/wiki/TorrentPier) - Torrent Tracker based on rutracker.org #
# Needs PHP 5.3 or higher #

<div><img src='http://torrentpier.info/styles/prosilver/imageset/logo.png' /></div>

## Supports databases ##
  * [Drizzle7](http://drizzle.org/)
  * [MySQL => 5.2 and higher](http://www.mysql.com/downloads/mysql/)
  * [Percona MariaDB 5.2 and higher](http://mariadb.org/)

## Search Engine ##
  * (WHERE ... LIKE ...)
  * [Sphinx Search](http://sphinxsearch.com/)

## PHP modules required ##
  * MySQL <font color='green'>(needed)</font>*** pcre**<font color='green'>(needed)</font>*** gd**<font color='green'>(needed)</font>*** iconv**<font color='green'>(needed)</font>*** json**<font color='blue'>(recommended)</font>*** mbstring**<font color='green'>(needed)</font>*** session**<font color='green'>(needed)</font>*** zlib**<font color='black'>(not recommended, needs compress on Apache, ngix etc... backend)</font>

## Cache methods ##
  * [APC (Advanced PHP Cache)](http://pecl.php.net/package/APC)
  * [MemCached](http://memcached.org/)
  * [XCache](http://xcache.lighttpd.net/)
  * [eAccelerator](http://eaccelerator.net/)
  * [SQLite](http://ua2.php.net/sqlite)
