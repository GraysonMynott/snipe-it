#!/bin/bash

sudo docker container stop snipe-it_app_1
sudo docker container rm snipe-it_app_1
sudo docker volume rm snipe-it_storage

sudo docker-compose up -d

