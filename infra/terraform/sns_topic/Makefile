export ENV ?= prod


.DEFAULT_GOAL := help

setup:
	pipenv install --dev
	# Following are not needed, since we're using any non-standard libraries,
	# and thus don't need to package them into a lambda layer
	#@mkdir -p layer/python
	#pipenv run pip freeze > requirements.txt
	#python3 -m pip install -r ./requirements.txt -t ./layer/python --upgrade

init:  ## Initialize terraform
	tfenv install # ensure correct terraform version installed
	terraform init -backend-config=backend/$(ENV).tfbackend

plan:  ## Create terraform plan
	@terraform validate
	terraform plan -out=tfplan -var-file=vars/$(ENV).tfvars
	@terraform show -no-color tfplan > tfplan.txt

deploy: plan
	@terraform show -no-color tfplan > tfplan.txt
	terraform apply tfplan
	@terraform output

destroy:
	@terraform destroy -var-file=vars/$(ENV).tfvars --auto-approve

clean:
	rm -rf tfplan tfplan.txt terraform.tfstate* .terraform

test:
	@echo "No tests yet"

readme:  ## Create documentation
	terraform-docs markdown . > docs/terraform.md
	@cat docs/header.md docs/terraform.md docs/footer.md > README.md
	@rm docs/terraform.md

help:  ## Display usage information
	@echo 'Usage:'
	@echo '  make <target>'
	@echo
	@echo 'Targets:'
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'


.PHONY: clean setup init plan apply destroy test readme help
