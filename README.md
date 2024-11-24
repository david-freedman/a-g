## Як запустити локально?

1. Встановити [Git](https://git-scm.com/downloads) та [Docker](https://www.docker.com/products/docker-desktop)

2. Клонувати репозиторій на локальний комп'ютер
    ```shell
    git clone git@github.com:david-freedman/a-g.git
    ```

3. Перейти в директорію проекту
    ```shell
    cd a-g
    ```

4. Перейменувати файл `config-dist.php` в `config.php` та вказати в ньому дані для підключення до бази даних
    ```shell
    cp config-dist.php config.php
    ```
5. Таку ж дію виконати для файлу `config-dist.php` в директорії `admin`
    ```shell
    cp admin/config-dist.php admin/config.php
    ```
6. Скопіювати файл `.env.example` в `.env` та вказати в ньому дані для підключення до бази даних локальної та на сервері
    ```shell
    cp .env.example .env
    ```
7. Запустити контейнери
    ```shell
    docker compose up -d
    ```
8. Надати права на запис для директорій `system/storage/cache`, `system/storage/logs`, `system/storage/session`, `system/storage/upload` та `system/storage/download`
	```shell
	docker compose exec apache chmod -R 777 system/storage/cache system/storage/logs system/storage/session system/storage/upload system/storage/download
	```
9. Надати права на запис для директорії `image`
	```shell
	docker compose exec apache chmod -R 777 image
	```
9. Для синхронізації бази даних виконати команду
	```shell
	docker compose run --rm db-sync
	```
