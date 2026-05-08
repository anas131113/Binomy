#!/bin/bash
set -e

CONTAINER="deploy-app-spring-angular-mysql-1"
DIR="/home/malak/binomy"

echo "=== Étape 1 : Build de l'image PHP avec PDO MySQL ==="
docker build -t binomy-php "$DIR"

echo "=== Étape 2 : Copie du schéma dans le conteneur MySQL ==="
docker cp "$DIR/db/schema.sql" "$CONTAINER:/tmp/schema.sql"

echo "=== Étape 3 : Import du schéma ==="
docker exec "$CONTAINER" mysql -uroot -proot -e "CREATE DATABASE IF NOT EXISTS binomy CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
docker exec "$CONTAINER" mysql -uroot -proot binomy -e "source /tmp/schema.sql"

echo "=== Étape 4 : Vérification des tables ==="
docker exec "$CONTAINER" mysql -uroot -proot binomy -e "SHOW TABLES;"

echo "=== Étape 5 : Création du compte admin ==="
docker run --rm -v "$DIR":/var/www/html --network host binomy-php php /var/www/html/db/seed.php

echo ""
echo "=== SETUP TERMINÉ ==="
echo "Lancer le serveur avec :"
echo "  bash ~/binomy/start.sh"
