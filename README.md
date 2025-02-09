# Запуск и начальная настройка

 Сборка контейнера:
 ```
 docker-compose up -d --build
 ```
 
 Копируем файл с переменными окружения:
 ```
 docker-compose exec app cp .env.example .env
 ```
 Установка зависимостей:
 ```
 docker-compose exec app composer install
 ```
 
 Применяем миграции:
 ```
 docker-compose exec app php artisan migrate
 ```

 Создаем ключ:
 ```
 docker-compose exec app php artisan key:generate
 ```
 
Создаем админа для админ-панели:
 ```
 docker-compose exec app php artisan orchid:admin admin admin@admin.com password
 ```

 
Запуск очереди:
 ```
 docker-compose exec app php artisan queue:work
 ```
http://localhost:8080/admin - orchid
http://localhost:8025 - mailhog
http://localhost:8081 - phpMyAdmin

- user - laravel_user
- password - laravel_password

## api

Eсть один get запрос http://localhost:8080/api/stock
У него следующие фильтры:

- year_from и year_to - год выпуска от и до
- price_less и price_higher - цена от и до
- brand и model - бренд и модель(тут используется like)
- vin - уникальный номер машины(тут точное совпадение)

## Orchid
Во вкладке Orders есть заказы которые не удалось отправить в crm 
