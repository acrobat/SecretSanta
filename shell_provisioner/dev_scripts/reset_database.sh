#!/bin/bash

/vagrant/htdocs/bin/console doctrine:database:drop --force
/vagrant/htdocs/bin/console doctrine:database:create
echo "Importing new secretsanta database"
zcat /vagrant/shell_provisioner/resource/secretsanta.sql.gz | mysql -uroot -pvagrant secretsanta
echo "Database imported succesfully"
