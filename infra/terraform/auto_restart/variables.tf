variable "name" { type = string }
variable "app" { type = string }
variable "component" { type = string }

variable "env" {
  type    = string
  default = "dev"
}

variable "schedule" {
  type        = string
  description = "The cron expression for the lambda function"
}

variable "lambda_log_level" {
  default = "INFO"
}

variable "lambda_handler" {
  type    = string
  default = "main.handler"
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

