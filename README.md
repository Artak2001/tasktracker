# TaskTracker — запуск на Windows (Apache + PHP + MySQL)

## Требования
- Windows 10/11 (64-bit)
- Apache 2.4 (распакован в `C:\Apache24`)
- PHP 8.x (распакован в `D:\php`)
- MySQL 9.x (распакован в `D:\mysql`)

## 1. Настройка Apache
1. Открой файл `C:\Apache24\conf\httpd.conf`.
2. Добавь строки для подключения PHP (рядом с другими `LoadModule`):
   ```apache
   LoadModule php_module "D:/php/php8apache2_4.dll"
   PHPIniDir "D:/php"
   AddType application/x-httpd-php .php
   DirectoryIndex index.php index.html

3.Укажи корневую папку (htdocs уже есть):
DocumentRoot "C:/Apache24/htdocs"
<Directory "C:/Apache24/htdocs">
    Options Indexes FollowSymLinks
    AllowOverride All
    Require all granted
</Directory>

Управление Apache через консоль
cd C:\Apache24\bin
.\httpd.exe -k start     # старт
.\httpd.exe -k restart   # перезапуск
.\httpd.exe -k stop      # остановка

cd D:\mysql\bin

# очистить папку данных (если пустая установка)
Remove-Item D:\mysql\data\* -Recurse -Force -ErrorAction SilentlyContinue
New-Item -ItemType Directory -Path D:\mysql\data -Force | Out-Null

# инициализация без пароля
.\mysqld --initialize-insecure --basedir="D:\mysql" --datadir="D:\mysql\data"

# установка сервиса
.\mysqld --install MySQL --defaults-file="D:\mysql\my.ini"

# запуск
net start MySQL

4. Инициализация:
cd D:\mysql\bin
Remove-Item D:\mysql\data\* -Recurse -Force -ErrorAction SilentlyContinue
New-Item -ItemType Directory -Path D:\mysql\data -Force | Out-Null
.\mysqld --initialize-insecure --basedir="D:\mysql" --datadir="D:\mysql\data"
.\mysqld --install MySQL --defaults-file="D:\mysql\my.ini"
net start MySQL


5. Вход и пароль:
.\mysql -u root

ALTER USER 'root'@'localhost' IDENTIFIED BY 'root';
FLUSH PRIVILEGES;
EXIT;

6. Скопировать tasktracker в C:\Apache24\htdocs\
7. В config/app.php: 
return [
  'mysql_host' => '127.0.0.1',
  'mysql_port' => 3306,
  'mysql_user' => 'root',
  'mysql_pass' => 'root',
  'mysql_db'   => 'tasktracker',
];
8. Удалить install.lock (если есть). появляется когда создаешь бд через install.php
9. Установи http://localhost/tasktracker/public/install.php

Открыть проект:
http://localhost/tasktracker/public/

Логи:
Apache → C:\Apache24\logs\error.log
MySQL → D:\mysql\data\*.err

Управление сервисами
# Apache
cd C:\Apache24\bin
.\httpd.exe -k restart

# MySQL
net stop MySQL
net start MySQL

 
# 🐧 Запуск на Linux (Ubuntu/Mint/Debian)

1. Установка пакетов
sudo apt update
sudo apt install -y apache2 php libapache2-mod-php mysql-server php-mysql unzip

2. Проверка сервисов
systemctl status apache2
systemctl status mysql

Если не запущены:

sudo systemctl start apache2
sudo systemctl start mysql
sudo systemctl enable apache2
sudo systemctl enable mysql

# MYSQL
3. MySQL root пароль

Войти в MySQL:

sudo mysql

В консоли: mysql

ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'root';
FLUSH PRIVILEGES;
EXIT;
  
4. Настройка проекта

Скопировать папку проекта:

sudo cp -r ~/Desktop/tasktracker /var/www/html/
sudo chown -R www-data:www-data /var/www/html/tasktracker
sudo chmod -R 755 /var/www/html/tasktracker

5. Конфиг

В config/app.php:

return [
  'mysql_host' => '127.0.0.1',
  'mysql_port' => 3306,
  'mysql_user' => 'root',
  'mysql_pass' => 'root',
  'mysql_db'   => 'tasktracker',
];

6. Установка базы

Удалить старый install.lock, если есть:

sudo rm /var/www/html/tasktracker/install.lock

Открыть в браузере:

http://localhost/tasktracker/public/install.php
http://localhost/tasktracker/public/
