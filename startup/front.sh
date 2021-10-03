#!/usr/bin/env bash

sudo service apache2 start && echo "[start-front] Front started" || echo "[start-front] Front startup failed"
