#!/bin/bash

sudo docker build -t snipe-it .

./rebuild_containers.sh
