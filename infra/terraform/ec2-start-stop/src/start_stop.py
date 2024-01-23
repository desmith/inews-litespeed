#!/usr/bin/env python

import json
import os

import boto3

from lib import setup_logger

logger = setup_logger(label=__name__)

EMAIL_RECIPIENTS = os.getenv('EMAIL_RECIPIENTS', 'devaprastha@iskcon.org')
EMAIL_SENDER = os.getenv('EMAIL_SENDER', 'devaprastha@iskcon.org')
SNS_TOPIC_ARN = os.getenv('SNS_TOPIC_ARN')


def handler(event, context) -> dict:
    ec2 = boto3.client('ec2')

    logger.info(f'## EVENT: {event}')
    logger.info(f'## CONTEXT: {context}')

    source = event.get('source')
    logger.info(f'## source: {source}')

    filters = [
        {
            'Name': 'tag:auto_start_stop',
            'Values': ['true']
        },
        {
            'Name': 'tag:env',
            'Values': ['dev']
        }
    ]

    instances = []
    _res = ec2.describe_instances(Filters=filters)
    if not _res['Reservations']:
        return {'message': 'No instances found with the given filters'}

    all_instances = _res['Reservations'][0].get('Instances')
    for instance in all_instances:
        _id = instance['InstanceId']
        _state = instance['State']['Name']
        _name = None
        for tag in instance['Tags']:
            if tag['Key'] == 'Name':
                _name = tag['Value']
                break

        instances.append(
            {
                "id": _id,
                "state": _state,
                "name": _name
            }
        )

        logger.info(f'instance: {_id}, state: {_state}')

    if source == 'event_bus':
        _command = event.get('command')
        logger.info(f'EventBus command: {_command}')

        if _command == 'stop':
            _stop_these = []
            for _instance in instances:
                _instance['source'] = 'EventBus'
                if _instance['state'] == 'running':
                    _stop_these.append(_instance['id'])
                    _instance['state'] = "Stopped"
                else:
                    _instance['state'] = f"Instance {_instance['id']} was not running, thus could not be stopped ..."

            if _stop_these:  # make sure we have something to stop...
                ec2.stop_instances(InstanceIds=_stop_these)

    else:
        # called directly via URL...
        # _qstring = event.get('rawQueryString')
        # _qstring = event.get('queryStringParameters')
        # logger.info(f'_qstring: {_qstring}')
        # if _qstring and _qstring['action']:
        #     action = _qstring['action']
        #     logger.info(f'action: {action}')
        if _qstring := event.get('rawQueryString'):
            param, value = _qstring.split('=')
            logger.info(f'{param=}')
            logger.info(f'{value=}')

            if param == 'action' and value == 'stop':
                _stop_these = []
                for _instance in instances:
                    _instance['source'] = 'Lambda'
                    if _instance['state'] == 'running':
                        _stop_these.append(_instance['id'])
                        _instance['state'] = "Stopped"
                        logger.info(f'Stopping instance {_instance["id"]} ...')
                    else:
                        _instance['state'] = f"Instance {_instance['id']} was not running, thus could not be stopped ..."

                if _stop_these:  # make sure we have something to stop...
                    ec2.stop_instances(InstanceIds=_stop_these)

            # elif action == 'start':
            elif param == 'action' and value == 'start':
                _start_these = []
                for _instance in instances:
                    _instance['source'] = 'Lambda'
                    if _instance['state'] == 'stopped':
                        _start_these.append(_instance['id'])
                        _instance['state'] = "Started"
                        logger.info(f'Starting instance {_instance["id"]} ...')
                    else:
                        _instance['state'] = f"Instance {_instance['id']} was not stopped, thus could not be started ..."

                if _start_these:  # make sure we have something to start...
                    ec2.start_instances(InstanceIds=_start_these)

    if SNS_TOPIC_ARN:
        sns = boto3.client('sns')
        sns.publish(
            TopicArn=SNS_TOPIC_ARN,
            Message=json.dumps(instances)
        )

    return {
        'statusCode': 200,
        'body': json.dumps(instances),
    }


if __name__ == '__main__':
    handler({}, {})
