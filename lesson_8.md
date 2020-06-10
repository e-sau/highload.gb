1. Установить Zabbix Server.
```shell script
sudo wget https://repo.zabbix.com/zabbix/5.0/debian/pool/main/z/zabbix-release/zabbix-release_5.0-1+buster_all.deb
sudo dpkg -i zabbix-release_5.0-1+buster_all.deb
sudo apt update
sudo apt install zabbix-server-mysql zabbix-frontend-php zabbix-nginx-conf zabbix-agent
```  
```mysql
create database zabbix character set utf8 collate utf8_bin;
create user zabbix@localhost identified by 'pass';
grant all privileges on zabbix.* to zabbix@localhost;
```
```shell script
zcat /usr/share/doc/zabbix-server-mysql*/create.sql.gz | mysql -uzabbix -p zabbix
```
* Установил пароль DB /etc/zabbix/zabbix_server.conf
```
DBPassword=pass
```
* Настроил /etc/zabbix/zabbix_server.conf
```
listen 8081;
server_name _;
```
* Установил timezone /etc/zabbix/php-fpm.conf
```
php_value[date.timezone] = Europe/Moscow
```
```shell script
sudo systemctl restart zabbix-server zabbix-agent nginx php7.3-fpm
sudo systemctl enable zabbix-server zabbix-agent nginx php7.3-fpm
```

* /images/zabbix/zabbix.jpg

.2. Добавить шаблон мониторинга HTTP-соединений.  
```text
Configuration -> Hosts -> Zabbix Server -> Templates
Link new templates -> Template App HTTP Service -> Update
```
.3. Настроить мониторинг созданных в рамках курса виртуальных машин.  
```text
Configuration -> Hosts -> Create Host
Host name: VirtualBox
Groups: Virtual machines
Interfaces: Agent 127.0.0.1:80

Host -> Templates
Link new templates -> Template App HTTP Service -> Add
```
.4. Добавить шаблон мониторинга NGINX.  
```text
Configuration -> Hosts -> Create Host
Host name: Nginx
Groups: Templates/Applications
Interfaces: Agent 127.0.0.1:80

Host -> Templates
Link new templates -> Template App Nginx by HTTP -> Add
```
* Добавил кофигурацию nginx -> /etc/nginx/nginx.conf:
```text
server {
        listen localhost;
        server_name localhost;
        access_log off;

        location = /basic_status {
                stub_status;
                allow 127.0.0.1;
                allow ::1;
                deny all;
        }
}
```
* /images/zabbix/nginx.jpg