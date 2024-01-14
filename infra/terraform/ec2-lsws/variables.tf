variable "app_name" { type = string }
variable "component" { type = string }
variable "env" { type = string }
variable "ssm_param_ami_id" { type = string }
variable "keypair_name" { type = string }
#variable "security_group_id" { type = string }
variable "vpc_id" { type = string }
variable "subnet_id" { type = string }
variable "target_group_host_headers" { type = list(string) }
variable "ssh_conf_dir" { type = string }

variable "domain" {
  type    = string
  default = "iskconnews.org"
}

variable "associate_public_ip_address" {
  type    = bool
  default = true
}

variable "instance_type" {
  type    = string
  default = "t3.micro"
}

variable "ec2_instance_role" {
  type    = string
  default = "WebServerRole"
}

variable "extra_security_group_ids" {
  type    = list(string)
  default = []
}

variable "userdata" {
  type    = string
  default = ""
}

variable "volume_size" {
  type    = string
  default = "12"
}

variable "encrypted" {
  type    = bool
  default = false
}

variable "kms_key_arn" {
  type    = string
  default = ""
}

variable playbook_url {
  type    = string
  default = "s3://inews-terraform-playbooks/inews-webapp.yml"
}

variable "ec2_disable_api_termination" {
  type    = bool
  default = false
}

variable "https_listener_arn" {
  type    = string
  default = "arn:aws:elasticloadbalancing:us-east-1:793753096261:listener/app/ICG-ELB/ed8f2ac6e693c7b2/44d4726e5f6e62db"
}

variable "elb_listener_priority" {
  type    = number
  default = 101
}
