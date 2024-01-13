variable "ami_id" { type = string }
variable "subdomain" { type = string }
variable "main_domain" { type = string }
variable "env" { type = string }
variable "app_name" { type = string }
variable "keypair_name" { type = string }
#variable "security_group_id" { type = string }
variable "subnet_id" { type = string }

variable "instance_type" {
  type    = string
  default = "t3.micro"
}

variable "ec2_instance_role" {
  type    = string
  default = "WebServerRole"
}

variable "security_group_ids" {
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


variable "ec2_disable_api_termination" {
  type    = bool
  default = false
}
