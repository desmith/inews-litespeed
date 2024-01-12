AWS_DEFAULT_REGION ?= us-east-1
APP ?= inews
ENV ?= prod

VPC_ID := $(shell aws ec2 describe-vpcs --filters Name=tag:network,Values=primary --query 'Vpcs[].VpcId' --output text)
SUBNET_ID := $(shell aws ec2 describe-subnets --filters Name=vpc-id,Values=$(VPC_ID) Name=tag:network,Values=private --query 'Subnets[0].SubnetId' --output text)
SECURITY_GROUP_ID := $(shell aws ec2 describe-security-groups --filters Name=tag:Name,Values=adminSG Name=vpc-id,Values=$(VPC_ID) --query 'SecurityGroups[].GroupId' --output text)

AMI_ID := ami-0efb76882af9859da  # CentOS 9 Stream

clean:
	rm -rf .terraform *.tfstate* tfplan* tfplan.json

init:
	@echo "Initializing terraform..."
	@echo "Using environment: $(ENV)"
	terraform init -backend-config=backend/$(ENV).tfbackend

plan: validate
	terraform plan \
		-var app=$(APP) \
		-var-file=environments/$(ENV).tfvars \
		--out=tfplan

	terraform show -json tfplan > tfplan.json

deploy: plan
	terraform apply tfplan

destroy:
	terraform destroy \
		-var app=$(APP) \
		-var-file=vars/$(ENV).tfvars \
		-auto-approve

validate:
	terraform validate

.PHONY: clean init plan deploy destroy validate
