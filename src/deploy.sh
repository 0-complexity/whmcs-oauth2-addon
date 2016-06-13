#!/usr/bin/env bash
# First argument of this script should be the root password for this machine
# Second argument the remote host, e.g. root@123.213.132.158
# Third argument is the ssh port of the remote host
# Fourth argument is the name of the container whmcs is running in, e.g. whcms
# Fifth argument should be 'debug' if you want to upload to the dev server
set -euf
rootpassword=$1
remote_host=$2
remote_ssh_port=$3
container_name=$4
debug=$5
tar -cvzf custom_oauth2.tgz whmcs
scp  -P ${remote_ssh_port} custom_oauth2.tgz ${remote_host}:/tmp/
scp  -P ${remote_ssh_port} docker-deploy.sh ${remote_host}:/tmp/docker-deploy.sh
ssh -p ${remote_ssh_port} -t ${remote_host} "echo $rootpassword | sudo -S -i bash /tmp/docker-deploy.sh ${container_name} ${debug}"
rm -f custom_oauth2.tgz