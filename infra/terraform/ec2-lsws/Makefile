AWS_DEFAULT_REGION ?= us-east-1
APP ?= inews-litespeed
ENV ?= dev

VPC_ID := $(shell aws ec2 describe-vpcs --filters Name=tag:network,Values=primary --query 'Vpcs[].VpcId' --output text)
#SUBNET_ID := $(shell aws ec2 describe-subnets --filters Name=vpc-id,Values=$(VPC_ID) Name=tag:network,Values=private --query 'Subnets[0].SubnetId' --output text)
SUBNET_ID := 'subnet-5b4d9954'
#SECURITY_GROUP_ID := $(shell aws ec2 describe-security-groups --filters Name=tag:Name,Values=adminSG Name=vpc-id,Values=$(VPC_ID) --query 'SecurityGroups[].GroupId' --output text)

#AMI_ID := ami-0efb76882af9859da  # CentOS 9 Stream
SSM_KEY := '/inews/infra/litespeed/ami_id'
AMI_ID := $(shell aws ssm get-parameter --name $(SSM_KEY) --query "Parameter.Value" --output text)


clean:
	rm -rf .terraform *.tfstate* tfplan* tfplan.json

init:
	@echo "Initializing terraform..."
	@echo "Using environment: $(ENV)"
	terraform init -backend-config=backend/$(ENV).tfbackend --reconfigure

plan:
	terraform plan \
		-var ami_id=$(AMI_ID) \
		-var env=$(ENV) \
		-var app_name=$(APP) \
		-var subnet_id=$(SUBNET_ID) \
		-var-file=environments/$(ENV).tfvars \
		--out=tfplan

	terraform show -json tfplan > tfplan.json

deploy: plan
	terraform apply tfplan

destroy:
	terraform destroy \
		-var ami_id=$(AMI_ID) \
		-var env=$(ENV) \
		-var app_name=$(APP) \
		-var subnet_id=$(SUBNET_ID) \
		-var-file=environments/$(ENV).tfvars \
		-auto-approve

.PHONY: clean init plan deploy destroy
