#!/usr/bin/env bash

front_server=("25.52.112.72" "apache2")           # yasu
rabbit_server=("25.52.117.120" "rabbitmq-server") # jeff
back_server=("25.52.190.242" "apache2")           # yi-hsuan
database_server=("25.52.70.25" "mysql-server")    # jakub

ssh "${rabbit_server[0]}" "ps cax | grep ${rabbit_server[1]} > /dev/null"
if [ $? -eq 0 ]; then
    echo "${rabbit_server[1]} is running."
else
    echo "${rabbit_server[1]} is not running."
fi
