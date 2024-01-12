# inews-lightspeed


- ami-0efb76882af9859da (CentOS 9 Stream)

TODO:

3) Watchdog (watchdog) ?
4) Munin (munin-node) ?
5) Monit (monit) ?


Notes:
Certbot auto renewal timer is not started by default.
amazon-ebs.packer-ebs: Run `systemctl start certbot-renew.timer` to enable automatic renewals.


Optional:

- create terraform to instantiate EC2 instance
