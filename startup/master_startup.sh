#!/usr/bin/env bash

front_server=("25.52.112.72" "apache2")           # yasu
rabbit_server=("25.52.117.120" "rabbitmq-server") # jeff
back_server=("25.52.190.242" "apache2")           # yi-hsuan
database_server=("25.52.70.25" "mysql-server")    # jakub
start_services_user=("start-services-user" "start-services")

sshpass -p ${start_services_user[1]} ssh -l ${start_services_user[0]} ${rabbit_server[0]} "ps cax | grep ${rabbit_server[1]} > /dev/null"
if [ $? -eq 0 ]; then
    echo "${rabbit_server[1]} is running."
else
    echo "${rabbit_server[1]} is not running."
    sudo hamachi login
    sudo hamachi join it490-005-4 123
    sudo service rabbitmq-server start && echo "[start-rabbit] RabbitMQ started" || echo "[start-rabbit] RabbitMQ startup failed"
fi
