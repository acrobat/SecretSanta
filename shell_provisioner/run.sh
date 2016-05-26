#!/bin/bash

# Shell provisioner

MODULE_PATH='/vagrant/shell_provisioner/module'
CONFIG_PATH='/vagrant/shell_provisioner/config'

# IP for the vagrant VM
GUEST_IP='192.168.33.69'

# Set the variables below for your project

# 1) Set to your app's local domainname
APP_DOMAIN='sss.dev.intracto.com'

# 2) Modify config/apache/app.vhost.conf and config/apache/hosts.txt to use the
#    values for APP_DOMAIN and GUEST_IP set above

# 3) App DB name and credentials
APP_DBNAME='symfony'
APP_DBUSER='symonfy_rw'
APP_DBPASSWORD='root'

# Hostname used by postfix
POSTFIX_HOSTNAME='vagrantbox.dev.intracto.com'

# Adding an entry here executes the corresponding .sh file in MODULE_PATH
DEPENDENCIES=(
    debian
    tools
    vim
    php
    mysql
    apache
    phpmyadmin
    gulp
    app_symfony
    mailcatcher
)

for MODULE in ${DEPENDENCIES[@]}; do
    source ${MODULE_PATH}/${MODULE}.sh
done
