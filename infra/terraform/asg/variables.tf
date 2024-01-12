variable "app" {
  type    = string
  default = "inews-webapp"
}

variable "component" {
  type    = string
  default = "infra"
}

variable "env" {
  type    = string
  default = "dev"
}

variable "domain" {
  type    = string
  default = "iskconnews.org"
}

variable "vpc_id" {
  type    = string
  default = "vpc-ab18ccd0"
  validation {
    condition     = length(var.vpc_id) > 4 && substr(var.vpc_id, 0, 4) == "vpc-"
    error_message = "The vpc_id value must be a valid VPC id, starting with \"vpc-\"."
  }
}

#variable "ami_id" {
#  type    = string
#  default = ""
#  validation {
#    condition     = length(var.ami_id) > 4 && substr(var.ami_id, 0, 4) == "ami-"
#    error_message = "The ami_id value must be a valid AMI id, starting with \"ami-\"."
#  }
#
#}

variable "instance_type" {
  type    = string
  default = "t3.micro"
}

variable "ec2_keypair_name" {
  type    = string
  default = "IC-News"
}

variable "ec2_root_volume_size" {
  type    = number
  default = 8
}

variable "ec2_role" {
  type = string
}

variable "inews_ami_id_ssm_param" {
  type    = string
}

variable playbook_url {
  type    = string
  default = "s3://inews-terraform-playbooks/inews-webapp.yml"
}

variable "enable_autoscaling_schedule" {
  type        = bool
  description = "Attach a schedule to the auto-scaling group"
  default     = false
}

variable "as_schedule_down" {
  type        = string
  description = "Cron expression in UTC for when to scale down instances"
  default     = null
}

variable "as_schedule_up" {
  type        = string
  description = "Cron expression in UTC for when to scale up instances"
  default     = null
}

variable ec2_alarm_topic {
  type    = string
  default = "arn:aws:sns:us-east-1:793753096261:Alarms"
}

variable "ssh_conf_dir" {
  type    = string
}

variable "tags" {
  type    = map
  default = {
    Managed_by = "Terraform"
  }
}
