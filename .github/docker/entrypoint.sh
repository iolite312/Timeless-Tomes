#!/bin/bash

cd /app

service nginx start

exec "$@"