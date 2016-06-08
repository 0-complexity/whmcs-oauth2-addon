#!/usr/bin/env bash
set -euf
cd /tmp
rm -rf whmcs html
tar -xvf custom_oauth2.tgz
mv whmcs html
mv html/templates/itsyouonline html/templates/fusion
docker cp html whmcs:/var/www/
rm -rf html custom_oauth2.tgz docker-deploy-yves.sh