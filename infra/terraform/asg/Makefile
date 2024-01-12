ENV ?= dev
APP ?= inews-infra-inner
EC2_INSTANCE_ROLE ?= WebServerRole-inner
#AMI_PARAM_NAME ?= /aws/service/ami-amazon-linux-latest/al2023-ami-kernel-default-x86_64
#AMI_ID ?= $(shell aws ssm get-parameter --name $(AMI_PARAM_NAME) --query "Parameter.Value" --output text)
SSH_CONF_DIR ?= $(HOME)/.ssh/conf.d

clean:
	rm -rf .terraform *.tfstate* tfplan* tfplan.json

init:
	@echo "Initializing terraform..."
	@echo "Using environment: $(ENV)"
	terraform init -backend-config=environments/$(ENV).tfbackend

plan: validate
	terraform plan -var app=$(APP) -var ec2_role=$(EC2_INSTANCE_ROLE) -var ssh_conf_dir=$(SSH_CONF_DIR) -var-file=environments/$(ENV).tfvars --out=tfplan
	terraform show -json tfplan > tfplan.json

apply: plan
	terraform apply tfplan

destroy:
	terraform destroy -var app=$(APP) -var ec2_role=$(EC2_INSTANCE_ROLE) -var ssh_conf_dir=$(SSH_CONF_DIR) -var-file=environments/$(ENV).tfvars -auto-approve

validate:
	terraform validate

.PHONY: clean init plan apply destroy validate
