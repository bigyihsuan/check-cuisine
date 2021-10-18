#!/usr/bin/env bash

sudo hamachi login
sudo hamachi join it490-005-4 123
sudo service mysql start && echo "[start-mysql] MySql started" || echo "[start-mysql] MySql startup failed"
# sudo service apache2 start && echo "[start-mysql] apache2 started" || echo "[start-mysql] apache2 startup failed"
php "../database/database.php"
