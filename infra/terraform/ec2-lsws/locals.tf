locals {

  auto_start_stop = var.env == "dev" ? true : false
  instance_name = "${var.app_name}-${var.component}-${var.env}"
  target_group_name = var.env == "prod" ? "inews-infra" : "inews-infra-dev"

  user_data_vars = {
    app          = var.app_name
    component    = var.component
    env          = var.env
    playbook_url = var.playbook_url
  }

  security_group_ids = flatten([
    var.extra_security_group_ids,
    data.aws_security_group.admin.id,
    data.aws_security_group.webapp.id,
    data.aws_security_group.waf.id
  ])

  common_tags   = {
    app       = var.app_name
    component = var.component
    env       = var.env
    Environment = var.env
    Terraform   = true
  }

}
