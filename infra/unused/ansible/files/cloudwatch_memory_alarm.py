#!/usr/bin/env python3
import urllib.request

import boto3

instance_id = urllib.request.urlopen('http://169.254.169.254/latest/meta-data/instance-id').read().decode()
print(f'InstanceId:{instance_id}')
ami_id = urllib.request.urlopen('http://169.254.169.254/latest/meta-data/ami-id').read().decode()
print(f'ImageId:{ami_id}')
instance_type = urllib.request.urlopen('http://169.254.169.254/latest/meta-data/instance-type').read().decode()
print(f'InstanceType:{instance_type}')
print('attempting creation of boto3 cloudwatch client...')
cloudwatch = boto3.client('cloudwatch', region_name='us-east-1')
print('cloudwatch client created')

print('creating cloudwatch memory utilization alarm')
response = cloudwatch.put_metric_alarm(
    AlarmName=f'{instance_id}-95-memory-alarm',
    ComparisonOperator='GreaterThanThreshold',
    EvaluationPeriods=1,
    MetricName='mem_used_percent',
    Namespace='CWAgent',
    Period=300,
    Statistic='Average',
    Threshold=95,
    AlarmActions=[
        'arn:aws:sns:us-east-1:108162735891:dispatch_create_ec2_alarms'
    ],
    AlarmDescription='Alarm when memory utilization is over 95%',
    Dimensions=[
        {
            'Name': 'InstanceId',
            'Value': instance_id
        },
        {
            'Name': 'ImageId',
            'Value': ami_id,
        },
        {
            'Name': 'InstanceType',
            'Value': instance_type,
        }
    ],
)
print(response)
