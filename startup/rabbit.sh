#!/usr/bin/env bash

sudo service rabbitmq-server start && echo "[start-rabbit] RabbitMQ Started" || echo "[start-rabbit] RabbitMQ startup failed"