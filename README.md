# Запуск и начальная настройка

 Сборка контейнера:
 ```
 docker-compose up -d --build
 ```
 
 Копируем файл с переменными окружения:
 ```
 docker-compose exec cp .env.example .env
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
 docker-compose exec php artisan orchid:admin admin admin@admin.com password
 ```


## api

Eсть один get запрос http://localhost:8080/api/stock
У него следующие фильтры:

- year_from и year_to - год выпуска от и до
- price_less и price_higher - цена от и до
- brand и model - бренд и модель(тут используется like)
- vin - уникальный номер машины(тут точное совпадение)

## Orchid
Во вкладке Orders есть заказы которые не удалось отправить в crm 