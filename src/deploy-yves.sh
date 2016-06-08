#!/usr/bin/env bash
# First argument of this script should be the password of the cloudscalers user on the machine hosting the docker WHMCS container
set -euf
rootpassword=$1
tar -cvzf custom_oauth2.tgz whmcs
scp -P 2222 custom_oauth2.tgz cloudscalers@85.255.197.104:/tmp/
scp -P 2222 docker-deploy-yves.sh cloudscalers@85.255.197.104:/tmp/docker-deploy.sh
ssh -p 2222 -t cloudscalers@85.255.197.104 "echo $rootpassword | sudo -S -i bash /tmp/docker-deploy.sh"
rm -f custom_oauth2.tgz