AMI_NAME := "inews-infra-litespeed"
AWS_DEFAULT_REGION ?= us-east-1
APP_NAME ?= inews
COMPONENT ?= litespeed
ENV ?= dev

PACKER_TEMPLATE ?= packer.json.pkr.hcl

VPC_ID := $(shell aws ec2 describe-vpcs --filters Name=tag:network,Values=primary --query 'Vpcs[].VpcId' --output text)
#SUBNET_ID := $(shell aws ec2 describe-subnets --filters Name=vpc-id,Values=$(VPC_ID) Name=tag:sx_network,Values=private --query 'Subnets[0].SubnetId' --output text)
#SECURITY_GROUP_ID := $(shell aws ec2 describe-security-groups --filters Name=tag:sx_name,Values=all-sg Name=vpc-id,Values=$(VPC_ID) --query 'SecurityGroups[].GroupId' --output text)
SUBNET_ID := subnet-5b4d9954

#SOURCE_AMI_PARAM ?= /aws/service/ami-amazon-linux-latest/al2023-ami-kernel-default-x86_64
#export AMI_ID ?= $(shell aws ssm get-parameter --name "$(SOURCE_AMI_PARAM)" --query "Parameter.Value" --output text)

AMI_ID := "ami-0efb76882af9859da"  # (CentOS 9 Stream)

export PACKER_LOG := 1
export PACKER_LOG_PATH := packerlog.txt

init:
	packer init $(PACKER_TEMPLATE)

clean:
	rm -rf tmp packer_cache packer-* $(PACKER_LOG_PATH) ami.txt manifest.json

validate:
	packer validate -var aws_region=$(AWS_DEFAULT_REGION) \
		-var app_name=$(APP_NAME) \
		-var component=$(COMPONENT) \
		-var env=$(ENV) \
		-var ami_name=$(AMI_NAME) \
		-var source_ami=$(AMI_ID) \
		-var subnet_id=$(SUBNET_ID) \
		-var vpc_id=$(VPC_ID) \
		$(PACKER_TEMPLATE)

build: clean init validate
	which packer
	packer --version

	packer build -var aws_region=$(AWS_DEFAULT_REGION) \
		-var app_name=$(APP_NAME) \
		-var component=$(COMPONENT) \
		-var env=$(ENV) \
		-var ami_name=$(AMI_NAME) \
		-var source_ami=$(AMI_ID) \
		-var subnet_id=$(SUBNET_ID) \
		-var vpc_id=$(VPC_ID) \
		$(PACKER_TEMPLATE)

plan:
	terraform plan -var-file=variables.tfvars

deploy:
	terraform apply -var-file=variables.tfvars
	 #attaches newly created instance to EIP (tags: app=inews, server=lightspeed)

.PHONY: build plan deploy validate clean
