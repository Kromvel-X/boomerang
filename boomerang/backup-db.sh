#!/bin/bash

# Создаём папку для бэкапов
mkdir -p boomerang/backups

# Делаем бэкап базы без ошибок
docker exec mysql_container mysqldump --user=root --password=rootpass --all-databases --single-transaction --quick --lock-tables=false > boomerang/backups/db_$(date +%F_%H-%M-%S).sql

echo "✅ Бэкап сохранён: boomerang/backups/db_$(date +%F_%H-%M-%S).sql"
