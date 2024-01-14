data "aws_ssm_parameter" "inews_ami_id" {
  name = var.ssm_param_ami_id
}

data "aws_ami" "inews-infra" {
  most_recent = true
  owners      = ["self"]

  filter {
    name   = "image-id"
    values = [data.aws_ssm_parameter.inews_ami_id.value]
  }
}
