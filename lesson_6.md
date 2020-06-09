* Построить NGINX-балансировку между двумя виртуальными машинами. Доказать, что балансировка происходит.
---
1 Настроил три файла конфигурации nginx:  

_upstream.conf_
```
upstream backend {
        server a.mysite.local:8081;
        server b.mysite.local:8080;
}

server {
        listen 80;
        server_name mysite.local;

        location / {
                proxy_pass http://backend;
        }
}
```
_a.mysite.local.conf_  
```
server {
        listen 8081;
        server_name a.mysite.local;
        root /var/www/a.mysite.local;
        index index.php;

        location ~* .(jpg|jpeg|gif|css|png|js|ico|html)$ {
                access_log off;
                expires max;
        }

        location / {
                try_files $uri $uri/ /index.php?$query_string;
        }

        location ~* .php$ {
                try_files $uri = 404;
                fastcgi_split_path_info ^(.+.php)(/.+)$;
                fastcgi_pass unix:/run/php/php7.3-fpm.sock;
                fastcgi_index index.php;
                fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
                include fastcgi_params;
        }
}
```
_b.mysite.local.conf_  
```
server {
        listen 8080;
        server_name b.mysite.local;
        root /var/www/b.mysite.local;
        index index.php;

        location ~* .(jpg|jpeg|gif|css|png|js|ico|html)$ {
                access_log off;
                expires max;
        }

        location / {
                try_files $uri $uri/ /index.php?$query_string;
        }

        location ~* .php$ {
                try_files $uri = 404;
                fastcgi_split_path_info ^(.+.php)(/.+)$;
                fastcgi_pass unix:/run/php/php7.3-fpm.sock;
                fastcgi_index index.php;
                fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
                include fastcgi_params;
        }
}
```

2 В папке /var/www/ создал две папки a.mysite.local и b.mysite.local  
3 В папки довавил файлы index.php  
4 Добавил записи в файл hosts  
5 На хост машине также добавил запись в файл hosts (127.0.0.1 mysite.local)  
6 Теперь каждый запрос по адресу mysite.local на хост-машине перенаправляется то на сервер a.mysite.local, то на сервер b.mysite.local   
---
* Реализовать альтернативное хранение сессий в Memcached.
---
Изменил настройки в файле php.ini
```
session.save_handler = memcached
session.save_path = "127.0.0.1:11211"
```
Проверил сохранение сессии:
```
get memc.sess.key.ni58sfd3urpq75u0tvbf6a0ido
VALUE memc.sess.key.ni58sfd3urpq75u0tvbf6a0ido 0 20
time|s:8:"19:31:44";
```
---
* Настроить NGINX для работы с символьной ссылкой.
---
1 В файле конфигурации сервера a.mysite.local в nginx изменил настройки:
```
root /var/www/sym.mysite.local;
```
2 Создал символическую ссылку на каталог a.mysite.local:
```
sudo ln -s a.mysite.local sym.mysite.local
```