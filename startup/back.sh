#!/usr/bin/env bash

sudo hamachi login
sudo hamachi join it490-005-4 123
sudo service apache2 start && echo "[start-back] Back started" || echo "[start-back] Back startup failed"
