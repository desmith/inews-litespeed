#!/usr/bin/env python3
""" This script manages tags for disk alarms """
import json
import logging
import os
import subprocess

import jmespath

logging.basicConfig(
    level=logging.INFO, format="%(asctime)-15s %(levelname)s %(message)s"
)
logger = logging.getLogger(__name__)
logger.setLevel(logging.INFO)

SNS_TOPIC_NAME = os.getenv("SNS_TOPIC_NAME", "dispatch_create_cwa_disk_alarms")


def get_tag_value(instance_id, region, tag_key="aws:autoscaling:groupName"):
    cmd = [
        "aws ec2",
        "describe-tags",
        f"--region {region}",
        "--filters",
        f"Name=resource-id,Values={instance_id}",
        "--query",
        f"'Tags[?Key==`{tag_key}`].Value'",
        "--output",
        "text",
    ]
    return subprocess.check_output(" ".join(cmd), shell=True).rstrip()


def get_ec2_metadata(query=None, path="dynamic/instance-identity/document"):
    url = f"http://169.254.169.254/latest/{path}"
    metadata = subprocess.check_output(["curl", "-s", url])
    # Convert from JSON
    metadata = json.loads(metadata)
    if query:
        metadata = jmespath.search(query, metadata)

    return metadata


def get_alarm_configuration(instance_id, asg_name, image_id, instance_type):
    current_mounts = []
    mount_points = subprocess.check_output(["mount", "-l"]).split("\n")
    for mount in mount_points:
        parts = mount.split()
        if len(parts) > 2 and parts[0].startswith("/dev"):
            logger.info("Mount point: %s on %s", parts[0], parts[2])
            device = {
                "Namespace": "CWAgent",
                "MetricName": "disk_used_percent",
                "ComparisonOperator": "GreaterThanOrEqualToThreshold",
                "Dimensions": [
                    {"Name": "path", "Value": parts[2]},
                    {"Name": "InstanceId", "Value": instance_id},
                    {"Name": "ImageId", "Value": image_id},
                    {"Name": "InstanceType", "Value": instance_type},
                    {"Name": "device", "Value": parts[0].split("/")[-1]},
                    {"Name": "fstype", "Value": parts[4]},
                ],
                "Period": 300,
                "EvaluationPeriods": 1,
                "Statistic": "Maximum",
                "Unit": "Percent"
            }
            if asg_name:
                device["Dimensions"].append({"Name": "AutoScalingGroupName", "Value": asg_name})
            current_mounts.append(device)

    return current_mounts


def post(alarm, region, account_id):
    topic_arn = f"arn:aws:sns:{region}:{account_id}:{SNS_TOPIC_NAME}"
    sns_message = {"default": json.dumps(alarm)}

    cmd = f"aws sns publish --region {region} --topic-arn {topic_arn} --message-structure json --message '{json.dumps(sns_message)}'"
    logger.info(cmd)

    return subprocess.check_output(cmd, shell=True).rstrip()


def main():
    """ main entry point """
    (region, account_id, instance_id, image_id, instance_type) = get_ec2_metadata(
        query="[region, accountId, instanceId, imageId, instanceType]"
    )
    asg_name = get_tag_value(instance_id, region)

    cache_dir = "/var/local/ansible/cw"
    # Contains the previous mount table
    cache = f"{cache_dir}/fs_cache-{instance_id}"
    # Contains the current mount table
    new_cache = f"{cache}.new"

    # Create the cache directory
    if not os.path.exists(cache_dir):
        os.makedirs(cache_dir)

    # Load the previous mount table
    previous_mounts = []
    if os.path.exists(cache):
        with open(cache) as in_file:
            previous_mounts = json.load(in_file)

    # Get the current mount table
    current_mounts = get_alarm_configuration(
        instance_id, asg_name, image_id, instance_type
    )

    # Save current mount table
    with open(new_cache, "w") as out_file:
        json.dump(current_mounts, out_file)

    if delta := [x for x in previous_mounts if x not in current_mounts] + [
        y for y in current_mounts if y not in previous_mounts
    ]:
        logger.info("There were changes -- %s", delta)
        post(current_mounts, region, account_id)

        # Refresh cache
        os.rename(new_cache, cache)
    else:
        logger.info("No changes")


if __name__ == "__main__":
    main()
