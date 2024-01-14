locals {
  common_tags = {
    app = var.app_name
    component = var.component
    env = var.env
  }
}

resource "aws_instance" "inews" {
  ami           = data.aws_ami.inews-infra.id
  instance_type = var.instance_type
  key_name      = var.keypair_name
  user_data     = base64encode(templatefile("./files/userdata.sh", local.boot_template_vars))
  root_block_device {
    volume_type = "gp3"
    volume_size = var.volume_size
    encrypted   = var.encrypted
    kms_key_id  = var.kms_key_arn
  }
  iam_instance_profile   = var.ec2_instance_role
  subnet_id              = var.subnet_id
  vpc_security_group_ids = local.security_group_ids
  #  vpc_security_group_ids  = flatten([join(", ", var.extra_security_group_ids), var.security_group_id])
  disable_api_termination = var.ec2_disable_api_termination
  metadata_options {
    http_endpoint = "enabled"
    http_tokens   = "required"
  }

  tags = merge(
    local.common_tags,
    {
      Name = local.instance_name
    }
  )
  volume_tags = merge(
    local.common_tags,
    {
      Name = local.instance_name
    }
  )

}
