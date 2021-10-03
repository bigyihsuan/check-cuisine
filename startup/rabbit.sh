#!/usr/bin/env bash

sudo hamachi login
sudo hamachi join it490-005-4 123
sudo service rabbitmq-server start && echo "[start-rabbit] RabbitMQ started" || echo "[start-rabbit] RabbitMQ startup failed"