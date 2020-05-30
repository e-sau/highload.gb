* Nginx config
```markup
upstream backend {
        server 127.0.0.1:8080;
}

server {
        listen 80;
        server_name highload.gb;

        location / {
                set             $memcached_key "$uri?$args";
                memcached_pass  127.0.0.1:11211;
                error_page      404 502 504 = @fallback;
        }

        location @fallback {
                proxy_pass      http://backend;
        }
}

server {
        listen 8080;
        server_name highload.gb;

        root /media/highload.gb;

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
                fastcgi_pass unix:/var/run/php/php7.3-fpm.sock;
                fastcgi_index index.php;
                fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
                include fastcgi_params;
        }
}
```
