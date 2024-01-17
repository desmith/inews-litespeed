output "lambda_arn" {
  value = aws_lambda_function.lambda.arn
}

output "trigger_schedule" {
  value = aws_cloudwatch_event_rule.stop_instances.schedule_expression
}

output "function_url" {
  value = aws_lambda_function_url.latest.function_url
}

#output "base_url" {
#  value = aws_api_gateway_deployment.default.invoke_url
#}
#
#output "api_custom_domain" {
#  value = "https://${aws_route53_record.restart.name}"
#}
