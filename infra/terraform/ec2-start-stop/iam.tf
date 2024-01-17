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
    sid    = "DescribeInstances"
    effect = "Allow"

    resources = [
      "*"
    ]

    actions = [
      "ec2:DescribeInstances",
      "ec2:StartInstances",
      "ec2:StopInstances",
      "sns:Publish"
    ]
  }

  statement {
    sid    = "AllowCreatingLogGroups"
    effect = "Allow"

    resources = [
      "arn:aws:logs:*:*:*"
    ]

    actions = [
      "logs:CreateLogGroup"
    ]
  }

  statement {
    sid    = "AllowWritingLogs"
    effect = "Allow"

    resources = [
      "arn:aws:logs:*:*:log-group:/aws/lambda/${local.function_name}:*"
    ]

    actions = [
      "logs:CreateLogStream",
      "logs:PutLogEvents",
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
