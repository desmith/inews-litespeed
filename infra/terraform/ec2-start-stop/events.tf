resource "aws_cloudwatch_event_rule" "stop_instances" {
  name                = "${local.function_name}-StopInstance"
  description         = "Stop lsws dev instances nightly"
  schedule_expression = var.stop_schedule
}

resource "aws_cloudwatch_event_target" "stop_instances" {
  rule      = aws_cloudwatch_event_rule.stop_instances.name
  target_id = "${local.function_name}-StopInstance"
  arn       = aws_lambda_function.lambda.arn
  input     = "{ \"source\": \"event_bus\", \"command\": \"stop\" }"
}

resource "aws_lambda_permission" "allow_events_bridge_to_run_lambda" {
  statement_id  = "AllowExecutionFromCloudWatch"
  action        = "lambda:InvokeFunction"
  function_name = local.function_name
  principal     = "events.amazonaws.com"
  depends_on    = [aws_lambda_function.lambda]
}
