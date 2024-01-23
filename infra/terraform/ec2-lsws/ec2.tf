data "aws_eip" "inews" {
  filter {
    name   = "tag:Name"
    values = [var.ec2_eip_name]
  }
}

resource "aws_eip_association" "eip_assoc" {
  instance_id   = aws_instance.inews.id
  allocation_id = data.aws_eip.inews.id
}

resource "aws_instance" "inews" {
  ami                         = data.aws_ami.inews-infra.id
  instance_type               = var.instance_type
  key_name                    = var.keypair_name
  ipv6_address_count          = 1
  associate_public_ip_address = true

  user_data = base64encode(templatefile("./files/userdata.sh", local.user_data_vars))
  root_block_device {
    volume_type = "gp3"
    iops        = 3000
    volume_size = var.volume_size
    encrypted   = var.encrypted
    kms_key_id  = var.kms_key_arn
  }
  iam_instance_profile    = var.ec2_instance_role
  subnet_id               = var.subnet_id
  vpc_security_group_ids  = local.security_group_ids
  #  vpc_security_group_ids  = flatten([join(", ", var.extra_security_group_ids), var.security_group_id])
  disable_api_termination = var.ec2_disable_api_termination
  metadata_options {
    http_endpoint = "enabled"
    http_tokens   = "required"
  }

  tags = merge(
    local.common_tags,
    {
      Name            = local.instance_name
      auto_start_stop = local.auto_start_stop
    }
  )
  volume_tags = merge(
    local.common_tags,
    {
      Name = local.instance_name
    }
  )

}
