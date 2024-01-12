locals {

  instance_name = "${var.app}-${var.component}-${var.env}.${var.domain}"
  #  global_tags = {
  #    Name        = "${var.env}.${var.domain}"
  #    Environment = var.env
  #    Terraform   = true
  #  }

  target_group_name = var.env == "prod" ? "inews-infra" : "inews-infra-dev"

  boot_template_vars = {
    app          = var.app
    component    = var.component
    env          = var.env
    playbook_url = var.playbook_url
  }

  #  tags = merge(local.global_tags, var.tags)

}

