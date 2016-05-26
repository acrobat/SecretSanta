#!/bin/bash

# Grunt

# Install node.js
curl -sL https://deb.nodesource.com/setup | bash -
apt-get install -y nodejs

# Update node packaged modules
npm update -g npm

# Install gulp
npm install --global gulp-cli

# nmp install dependencies
apt-get install -y g++ git
