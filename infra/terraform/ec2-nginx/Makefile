AWS_DEFAULT_REGION ?= us-east-1
ENV ?= dev

PACKER_TEMPLATE := packer.json.pkr.hcl

VPC_ID := $(shell aws ec2 describe-vpcs --filters Name=tag:network,Values=primary --query 'Vpcs[].VpcId' --output text)
SUBNET_ID := $(shell aws ec2 describe-subnets --filters Name=vpc-id,Values=$(VPC_ID) Name=tag:network,Values=private --query 'Subnets[0].SubnetId' --output text)
SECURITY_GROUP_ID := $(shell aws ec2 describe-security-groups --filters Name=tag:Name,Values=adminSG Name=vpc-id,Values=$(VPC_ID) --query 'SecurityGroups[].GroupId' --output text)

BASTION_KEY ?= ~/.ssh/icg/IC-Bastion.pem
BASTION_HOST_PARAM_NAME ?= /infra/BASTION_HOST_IP
BASTION_HOST_IP := $(shell aws ssm get-parameter --name $(BASTION_HOST_PARAM_NAME) --query "Parameter.Value" --output text)

export PACKER_LOG ?= 1
export PACKER_LOG_PATH ?= packerlog.txt

export APP ?= inews
export EC2_INSTANCE_ROLE ?= WebServerRole
AMI_PARAM_NAME = /aws/service/ami-amazon-linux-latest/al2023-ami-kernel-default-x86_64
AMI_ID := $(shell aws ssm get-parameter --name ${AMI_PARAM_NAME} --query "Parameter.Value" --output text)


init-packer:
	packer init $(PACKER_TEMPLATE)

init-terraform:
	cd terraform && make init

init: init-packer init-terraform

clean:
	cd terraform && make clean

test:
	@echo "No tests yet..."
	@echo "Need tests for terraform..."
	@echo "Need tests for ansible..."
	@echo "Dynamic AMI_ID: $(AMI_ID)"

build:
	rm -rf tmp packer_cache packer-* $(PACKER_LOG_PATH) manifest.json ami.txt
	@echo "Building AMI..."
	packer build -var aws_region=$(AWS_DEFAULT_REGION) \
		-var bastion_host_ip=$(BASTION_HOST_IP) \
		-var bastion_private_key=$(BASTION_KEY) \
		-var env=$(ENV) \
		-var name=$(APP) \
		-var iam_instance_profile=$(EC2_INSTANCE_ROLE) \
		-var security_group_id=$(SECURITY_GROUP_ID) \
		-var source_ami=$(AMI_ID) \
		-var subnet_id=$(SUBNET_ID) \
		-var vpc_id=$(VPC_ID) \
		$(PACKER_TEMPLATE) && ./scripts/ami2ssm.sh $(ENV)

plan:
	cd terraform && make plan

deploy:
	cd terraform && make apply

destroy:
	cd terraform && make destroy

validate:
	packer validate -var aws_region=$(AWS_DEFAULT_REGION) \
		-var bastion_host_ip=$(BASTION_HOST_IP) \
		-var bastion_private_key=$(BASTION_KEY) \
		-var env=$(ENV) \
		-var name=$(APP) \
		-var iam_instance_profile=$(EC2_INSTANCE_ROLE) \
		-var security_group_id=$(SECURITY_GROUP_ID) \
		-var source_ami=$(AMI_ID) \
		-var subnet_id=$(SUBNET_ID) \
		-var vpc_id=$(VPC_ID) \
		$(PACKER_TEMPLATE)
	@echo "Packer validate passed"
	cd terraform && make validate
	@echo "Terraform validate passed"

.PHONY: clean test init plan apply destroy validate build
