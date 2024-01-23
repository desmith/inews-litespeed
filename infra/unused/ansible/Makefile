
SOURCE_AMI_PARAM ?= /aws/service/ami-amazon-linux-latest/al2023-ami-kernel-default-x86_64
AMI_ID := $(shell aws ssm get-parameter --name "$(SOURCE_AMI_PARAM)" --query "Parameter.Value" --output text)


# export OBJC_DISABLE_INITIALIZE_FORK_SAFETY ?= YES
# the above variable needs to be set to run the test locally

init:
	pipenv install --deploy

galaxy:
	pipenv run ansible-galaxy install -r requirements.yml -p roles

test:
	AL2_AMI_ID=$(AMI_ID) pipenv run molecule test


.PHONY: galaxy setup test
