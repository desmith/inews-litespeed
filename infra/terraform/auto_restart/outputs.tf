output "lambda_arn" {
  value = aws_lambda_function.lambda.arn
}

output "trigger_schedule" {
  value = aws_cloudwatch_event_rule.schedule.schedule_expression
}

output "topic_arn" {
  value = data.aws_sns_topic.auto_restart.arn
}
