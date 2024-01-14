locals {

  instance_name = "${var.app_name}-${var.component}-${var.env}.${var.domain}"
  #  global_tags = {
  #    Name        = "${var.env}.${var.domain}"
  #    Environment = var.env
  #    Terraform   = true
  #  }

  target_group_name = var.env == "prod" ? "inews-infra" : "inews-infra-dev"

  boot_template_vars = {
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

  #  boot_server_tags = merge(local.global_tags, {
  #    Name = "boot-server-${var.env}"
  #  }
  #  tags = merge(local.global_tags, var.tags)

}
