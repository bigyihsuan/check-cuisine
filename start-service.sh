#!/bin/bash

# start service rabbitmq
service rabbitmq-server start && echo "[start-service] RabbitMQ Started" || echo "somethign else"

# start service mysql
service mysql start  && echo "[start-service] MySql Started" || echo "another else"