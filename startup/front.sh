#!/usr/bin/env bash

sudo hamachi login
sudo hamachi join it490-005-4 123
sudo service apache2 start && echo "[start-front] Front started" || echo "[start-front] Front startup failed"
