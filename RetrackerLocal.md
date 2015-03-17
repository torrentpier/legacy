# OpenBitTorrent - Ретрекер Установка на FreeBSD #

![http://openbittorrent.com/images/logo.jpg](http://openbittorrent.com/images/logo.jpg)

**OpenBitTorrent** это свободный (бесплатный) BitTorrent треккер для любого желающего. Где бы Вы не находились, Вам не нужно регистрироваться, скачивать или индексировать торрент, все что нужно - это ввести URL OpenBitTorrent в ваш торрент файл.

**HTTP = http://tracker.openbittorrent.kg:2710/announce**

**UDP = udp://tracker.openbittorrent.kg:2710/announce**

Cборка исходников и установка
```
$ cd /usr/ports/net/opentracker && make install clean
```
Конфигурирование /usr/local/etc/opentracker/opentracker.conf:
```
listen.tcp_udp 0.0.0.0:2710
access.stats_path sta
tracker.redirect_url http://re-tracker.ru/
```
Конфигурировние на запуск демона
```
$ opentracker_enable="YES" >> /etc/rc.conf
$ opentracker_config="/usr/local/etc/opentracker/opentracker.conf" >> /etc/rc.conf
```
Первый запуск демона (ретрекера)
```
$ /usr/local/etc/rc.d/opentracker start
```