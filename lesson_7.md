1 Установить RabbitMQ.
```
sudo apt install rabbitmq-server
sudo rabbitmq-plugins enable rabbitmq_management
sudo service rabbitmq-server restart
sudo apt install php7.3-mbstring
composer require "php-amqplib/php-amqplib
```
2 Создать несколько очередей.
```
Через UI создал несколько очередей: q1, q2, q3
```
3 Реализовать цепочку «Заказ еды — оплата — доставка — отзыв клиента». Сколько понадобится очередей?
