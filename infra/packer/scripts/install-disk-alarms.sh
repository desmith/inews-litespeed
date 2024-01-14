#!/usr/bin/env bash

set -e -o pipefail	# exit if there are any errors

echo "Download/install/run cloudwatch agent..."
dnf install -y https://s3.amazonaws.com/amazoncloudwatch-agent/amazon_linux/amd64/latest/amazon-cloudwatch-agent.rpm
mv /tmp/amazon-cloudwatch-agent.json /opt/aws/amazon-cloudwatch-agent/etc/amazon-cloudwatch-agent.json
/opt/aws/amazon-cloudwatch-agent/bin/amazon-cloudwatch-agent-ctl \
    -a fetch-config -m ec2 \
    -c file:/opt/aws/amazon-cloudwatch-agent/etc/amazon-cloudwatch-agent.json -s

echo "installing jmespath"
python3 -m pip install jmespath
echo "jmespath installed"

echo "creating cache store directory"
mkdir -p /var/local/ansible/cw
echo "cache store directory created"

echo "moving files to correct location..."
mv /tmp/post_disk_geometry /usr/local/bin/post_disk_geometry
echo "post_disk_geometry moved to /usr/local/bin/post_disk_geometry"

echo "adding disk_alarms to crontab..."
cat /tmp/disk_alarms >> /etc/crontab
rm -f /tmp/disk_alarms
#mv /tmp/disk_alarms /etc/cron.d/disk_alarms
#chown root:root /etc/cron.d/disk_alarms
echo "disk_alarms added to crontab"

echo "setting exec permissions on post_disk_geometry"
chmod +x /usr/local/bin/post_disk_geometry
echo "exec permissions correctly set on post_disk_geometry"
