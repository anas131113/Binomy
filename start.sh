#!/bin/bash
echo "Binomy démarré sur http://localhost:8000"
docker run --rm -v /home/malak/binomy:/var/www/html --network host binomy-php php -S 0.0.0.0:8000 -t /var/www/html
