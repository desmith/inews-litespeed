#!/usr/bin/env bash

echo "Enabling memory-alarm..."
mv /tmp/cloudwatch_memory_alarm /usr/local/bin/cloudwatch_memory_alarm
chmod +x /usr/local/bin/cloudwatch_memory_alarm
mv /tmp/memory-alarm.service /etc/systemd/system/memory-alarm.service
systemctl enable memory-alarm
