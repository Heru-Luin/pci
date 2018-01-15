#!/usr/bin/env bash

cd $(dirname $(realpath $0))
cd workspace && rm -rf ${2}
git clone ${1} ${2} && cd ${2}
git fetch && git checkout ${2}
export COMPOSER_HOME=~/.composer
/usr/local/bin/composer install && ./vendor/bin/phing > ../${2}.log
