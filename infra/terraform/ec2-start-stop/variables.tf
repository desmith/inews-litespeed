variable "sns_topic_name" { type = string }
variable "route53_zone_name" { type = string }

variable "name" {
  type = string
}

variable "env" {
  type    = string
  default = "dev"
}

variable "stop_schedule" {
  type        = string
  description = "The cron expression for the lambda function"
  default     = "cron(0 1 ? * * *)"
}

variable "email_recipients" {
  type        = string
  description = "Email recipients (comma separated if multiple)"
}

variable "email_sender" {
  type        = string
  description = "Email sender"
}

variable "lambda_log_level" {
  default = "INFO"
}

variable "lambda_handler" {
  type = string
}

variable "lambda_runtime" {
  type    = string
  default = "python3.12"
}

variable "compatible_runtimes" {
  type    = list(string)
  default = ["python3.9", "python3.10", "python3.11", "python3.12"]
}

variable "log_group_retention_in_days" {
  type    = number
  default = 90
}

