#!/bin/bash

mysqldump -u root -pvagrant secretsanta | gzip -9 > /vagrant/shell_provisioner/resource/secretsanta.sql.gz