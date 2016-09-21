#!/usr/bin/env bash

phpenv config-rm xdebug.ini || exit $? # Disable XDebug
mkdir -p \"${BUILD_CACHE_DIR}\" || exit $? # Create build cache directory

# Update composer to the latest stable release as the build env version is outdated
composer self-update --stable || exit $?
