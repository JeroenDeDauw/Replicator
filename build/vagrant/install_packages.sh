#!/bin/bash -x

add-apt-repository ppa:ondrej/php -y
apt-get update

# Avoid MySQL password prompt
export DEBIAN_FRONTEND="noninteractive"
debconf-set-selections <<< 'mysql-server mysql-server/root_password password PASSWORD_HERE'
debconf-set-selections <<< 'mysql-server mysql-server/root_password_again password PASSWORD_HERE'

apt-get install -y mysql-client mysql-server
apt-get install -y php7.1 php7.1-sqlite3 php7.1-mysql php7.1-xml php7.1-mbstring php7.1-zip
