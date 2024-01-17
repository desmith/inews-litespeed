#!/usr/bin/env python
import os

import boto3

from lib import setup_logger

logger = setup_logger(__name__)

region = 'us-east-1'
TOPIC_ARN = os.getenv('TOPIC_ARN')

ec2 = boto3.client('ec2', region_name=region)
sns = boto3.client('sns', region_name=region)

_filter = [
    {'Name': 'tag:auto_restart', 'Values': ['True']}
]

response = ec2.describe_instances(Filters=_filter)
instances = []
instance_names = []

for instance in response['Reservations']:
    for i in instance['Instances']:
        instances.append(i['InstanceId'])
        for tag in i['Tags']:
            if 'Name' in tag['Key']:
                _name = tag['Value']
                instance_names.append(_name)


def main(event, context):  # pylint: disable=unused-argument
    ret = ec2.reboot_instances(InstanceIds=instances)
    if ret['ResponseMetadata']['HTTPStatusCode'] == 200:
        print(f'Restarted instances: {instance_names} ({instances})')
        str_msg = f'Instances {instance_names} ({instances}) restarted by EventBus via Lambda'
    else:
        print(f'Error stopping instances: {instance_names} ({instances})')
        print(f'HTTPStatusCode: {ret["ResponseMetadata"]["HTTPStatusCode"]}')
        str_msg = f'Instances {instance_names} ({instances}) NOT restarted'
        str_msg += f'\nHTTPStatusCode: {ret["ResponseMetadata"]["HTTPStatusCode"]}'

    if TOPIC_ARN:
        msg_response = sns.publish(TopicArn=TOPIC_ARN, Message=str_msg)
        print(f'Message published: {msg_response}')
    else:
        logger.error('TOPIC_ARN not defined')


if __name__ == '__main__':
    main({}, {})
