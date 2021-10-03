#!/usr/bin/env bash

sudo service rabbitmq-server start && echo "[start-rabbit] RabbitMQ started" || echo "[start-rabbit] RabbitMQ startup failed"