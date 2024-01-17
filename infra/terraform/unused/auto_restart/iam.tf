data "aws_iam_policy_document" "assume-role-policy_document" {
  statement {
    actions = ["sts:AssumeRole"]

    principals {
      type        = "Service"
      identifiers = ["lambda.amazonaws.com"]
    }
  }
}

data "aws_iam_policy_document" "lambda_policy_doc" {
  statement {
    sid     = "AllowCreatingLogGroups"
    effect  = "Allow"
    actions = [
      "logs:CreateLogGroup"
    ]
    resources = [
      "arn:aws:logs:*:*:*"
    ]
  }

  statement {
    sid     = "PublishSNSMessage"
    effect  = "Allow"
    actions = [
      "sns:Publish"
    ]
    resources = [
      data.aws_sns_topic.auto_restart.arn
    ]
  }

  statement {
    sid    = "AllowWritingLogs"
    effect = "Allow"

    actions = [
      "logs:CreateLogStream",
      "logs:PutLogEvents",
    ]

    resources = [
      "arn:aws:logs:*:*:log-group:/aws/lambda/${local.function_name}:*"
    ]

  }

  statement {
    sid     = "StartStopEC2Instances"
    effect  = "Allow"
    actions = [
      "ec2:RebootInstances",
      "ec2:DescribeInstances"
    ]
    resources = [
      "*"
    ]
  }
}

resource "aws_iam_role" "lambda_execution_role" {
  name               = local.function_role_name
  path               = "/service-role/"
  assume_role_policy = data.aws_iam_policy_document.assume-role-policy_document.json

  inline_policy {
    name   = "${local.function_name}-policy"
    policy = data.aws_iam_policy_document.lambda_policy_doc.json
  }
}
