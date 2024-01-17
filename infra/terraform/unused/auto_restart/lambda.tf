locals {
  lambda_root        = "${path.module}/src"
  function_name      = "${var.app}-${var.component}-${var.name}"
  function_role_name = "${local.function_name}-role"
  tags               = {
    app        = var.app
    component  = var.component
    env        = var.env
    contact    = "devaprastha@iskcon.org"
    Terraform  = true
  }
}

# Configuring a layer resource (moved to Makefile)
#resource "null_resource" "install_dependencies" {
#  provisioner "local-exec" {
#    command = "python3 -m pip install -r ${path.module}/requirements.txt -t ${path.module}/layer/python --upgrade"
#  }
#}

# Creating a .zip with dependencies
#data "archive_file" "layer" {
#  type             = "zip"
#  output_file_mode = "0666"
#  source_dir       = "${path.module}/layer"
#  output_path      = "${path.module}/layer.zip"
#}

#resource "aws_lambda_layer_version" "layer" {
#  layer_name          = "python-dep-layer"
#  filename            = data.archive_file.layer.output_path
#  source_code_hash    = data.archive_file.layer.output_base64sha256
#  compatible_runtimes = var.compatible_runtimes
#}

# In order to ensure that cached versions of the Lambda aren't invoked by AWS,
# let's give our zip file a unique name for each version.
# We can do with by hashing our files together.
resource "random_uuid" "lambda_src_hash" {
  keepers = {
    for filename in setunion(
      fileset(local.lambda_root, "restart_instance.py"),
    ) :
    filename => filemd5("${local.lambda_root}/${filename}")
  }
}

# Creating a .zip with the code
data "archive_file" "src" {
  type             = "zip"
  output_file_mode = "0666"
  source_dir       = local.lambda_root
  #  output_path = "${path.module}/code.zip"
  output_path      = "${random_uuid.lambda_src_hash.result}.zip"

  excludes   = [
    "__pycache__",
  ]

}

# Create log group
resource "aws_cloudwatch_log_group" "lambda_logs" {
  name              = "/aws/lambda/${local.function_name}"
  retention_in_days = var.log_group_retention_in_days
  tags              = local.tags
}

# Creating a Lambda resource
resource "aws_lambda_function" "lambda" {
  function_name    = local.function_name
  description      = "This lambda generates a report of awsbackup jobs for the past ${var.log_group_retention_in_days} days."
  handler          = var.lambda_handler
  runtime          = var.lambda_runtime
  filename         = data.archive_file.src.output_path
  memory_size      = 128
  timeout          = 300
  source_code_hash = data.archive_file.src.output_base64sha256
  role             = aws_iam_role.lambda_execution_role.arn
#  layers           = [aws_lambda_layer_version.layer.arn]

  depends_on = [
    aws_cloudwatch_log_group.lambda_logs,
#    aws_lambda_layer_version.layer
  ]

    environment {
    variables = {
      TOPIC_ARN = data.aws_sns_topic.auto_restart.arn
    }
  }

  tags = local.tags

}

