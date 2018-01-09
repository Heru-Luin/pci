#!/usr/bin/env bash

cd workspace
git clone ${1} ${2} && cd ${2}
git fetch
git checkout ${2}
composer install
./vendor/bin/phing >> output_console.txt
