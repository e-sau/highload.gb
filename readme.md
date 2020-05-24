* Установка MariaDB
```shell script
sudo apt install mariadb-server 
```

* Пробросил порт 3306 в VirtualBox
* Установил MySQL Workbench

* Создал пользователя БД
```mysql
CREATE USER 'monitor'@localhost IDENTIFIED BY 'pass';
GRANT ALL PRIVILEGES ON *.* TO 'monitor'@'10.0.2.2' IDENTIFIED BY 'pass' WITH GRANT OPTION;
```
* Загрузил дамп БД через MySQL Workbench -> Data Import/Restore

* Какие ситуации, вызывающие рост количества запросов, могут случаться на сервере?  
_- неоптимальный запрос_  
_- неоптимальная структура таблиц_  
_- увеличение количества пользователей_  

* В каких случаях индекс в MySQL не будет применятся, даже если он доступен и выборка должна использовать его?  
_- если использование индекса требует от MySQL прохода более чем по 30% строк в данной таблице_  
_- если диапазон изменения индекса может содержать величины NULL при использовании выражений ORDER BY ... DESC_ 

* Как принудительно применить индекс?  
_Использовать в запросе выражения USE INDEX (index_list) или FORCE INDEX (index_list)_