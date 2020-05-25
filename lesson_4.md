* Собрать две виртуальные машины с установленным MySQL-сервером  
_повторил установку mariadb для второй виртуальной машины_  

* Развернуть репликацию на этих двух серверах  
1. Добавил новую сеть в VirtualBox. Master имеет ip = 10.1.2.4, slave = 10.1.2.5
2. Добавил следующую конфигурацию в файл my.cnf (master):  
```
server-id=1
log_bin=mysql-bin.log
binlog_do_db=skytech
```
3. Создаем пользователя для репликации
```mysql
GRANT REPLICATION SLAVE ON *.* TO 'slave_user'@'%' IDENTIFIED BY 'pass';
FLUSH PRIVILEGES;
```
4. Сделал дамп БД на master, перекинул дамп на slave.
5. Настроил slave:
```
server-id=2
relay-log=mysql-relay-bin.log
log_bin=mysql-bin.log
replicate_do_db=skytech
```
6. Включаем репликацию
```mysql
CHANGE MASTER TO MASTER_HOST='10.1.2.4', MASTER_USER='slave_user', MASTER_PASSWORD='pass',
MASTER_LOG_FILE = 'mysql-bin.000005', MASTER_LOG_POS = 342;
START SLAVE;
```

7. Бинго!  

* На двух виртуальных машинах создать два шарда БД. Создать логику общения с ними тестового PHP-скрипта — например, распределение новых пользователей по шардам.
_sharding.php_