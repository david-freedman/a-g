#!/bin/bash

# Всі доступи прописані в середовищі

# Використання змінних із середовища
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
BACKUP_FILE="/backup/database_backup_$TIMESTAMP.sql"

# Створення дампу продакшн-бази
echo "Створення дампу бази даних з продакшн-сервера..."
mysqldump --no-tablespaces -h $PROD_DB_HOST -u $PROD_USER -p$PROD_PASSWORD $PROD_DB > $BACKUP_FILE

if [ $? -ne 0 ]; then
    echo "Помилка: не вдалося створити дамп бази даних!"
    exit 1
fi

# Імпорт дампу в локальну базу
echo "Імпорт бази даних у локальну MySQL..."
mysql -h db -u $LOCAL_USER -p$LOCAL_PASSWORD $LOCAL_DB < $BACKUP_FILE

if [ $? -ne 0 ]; then
    echo "Помилка: не вдалося імпортувати дамп бази даних!"
    exit 1
fi

echo "Синхронізація завершена успішно. Файл дампу: $BACKUP_FILE"
