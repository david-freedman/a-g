FROM mysql:5.7

# Копіювання скрипта в контейнер
COPY sync_db.sh /usr/local/bin/sync_db.sh

# Створення папки для бекапів
RUN mkdir /backup

# Надання прав на виконання
RUN chmod +x /usr/local/bin/sync_db.sh

# Вказати команду за замовчуванням
CMD ["/usr/local/bin/sync_db.sh"]
