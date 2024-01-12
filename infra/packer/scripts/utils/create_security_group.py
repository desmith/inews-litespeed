#!/usr/bin/env python

"""Create a security group for quic.cloud IPs."""

import ipaddress
from pathlib import Path

import boto3

ec2 = boto3.client('ec2')

SG_ID = 'sg-006a133876cdd75fe'
IPS_FILE = Path(__file__).parent / 'fixtures' / 'quic.cloud.ips.txt'

with IPS_FILE.open() as ifile:
    ip_list = ifile.read().splitlines()
    ips = [ipaddress.IPv4Address(line) for line in ip_list]
    cidrs = [ip.with_prefixlen for ip in ipaddress.collapse_addresses(ips)]

cidr_list = []
for cidr in cidrs:
    cidr_entry = {
        'CidrIp': cidr,
        'Description': 'Allow from QUIC.cloud'
    }
    ec2.authorize_security_group_ingress(
        GroupId=SG_ID,
        IpPermissions=[
            {
                'IpProtocol': 'tcp',
                'FromPort': 1,
                'ToPort': 65535,
                'IpRanges': [cidr_entry]
            }
        ])
