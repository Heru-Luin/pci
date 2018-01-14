#!/usr/bin/env bash

cd workspace && rm -rf ${2}
git clone ${1} ${2} && cd $(basename $_ .git)
git fetch && git checkout ${2}
composer install && ./vendor/bin/phing >> ../${2}.log
