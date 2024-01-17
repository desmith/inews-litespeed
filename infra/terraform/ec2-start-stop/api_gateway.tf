#resource "aws_api_gateway_rest_api" "restart" {
#  name        = "EC2StartStop-lsws"
#  description = "Provides an interface to start/stop EC2 instance via Lambda"
#}
#
#
#resource "aws_api_gateway_resource" "proxy" {
#  rest_api_id = aws_api_gateway_rest_api.restart.id
#  parent_id   = aws_api_gateway_rest_api.restart.root_resource_id
#  path_part   = "{proxy+}"
#}
#
#resource "aws_api_gateway_method" "proxy" {
#  rest_api_id   = aws_api_gateway_rest_api.restart.id
#  resource_id   = aws_api_gateway_resource.proxy.id
#  http_method   = "GET"
#  authorization = "NONE"
#}
#
#resource "aws_api_gateway_integration" "lambda" {
#  rest_api_id = aws_api_gateway_rest_api.restart.id
#  resource_id = aws_api_gateway_method.proxy.resource_id
#  http_method = aws_api_gateway_method.proxy.http_method
#
#  integration_http_method = "GET"
#  type                    = "AWS_PROXY"
#  uri                     = aws_lambda_function.lambda.invoke_arn
#}
#
#resource "aws_api_gateway_method" "proxy_root" {
#  rest_api_id   = aws_api_gateway_rest_api.restart.id
#  resource_id   = aws_api_gateway_rest_api.restart.root_resource_id
#  http_method   = "GET"
#  authorization = "NONE"
#}
#
#resource "aws_api_gateway_integration" "lambda_root" {
#  rest_api_id = aws_api_gateway_rest_api.restart.id
#  resource_id = aws_api_gateway_method.proxy_root.resource_id
#  http_method = aws_api_gateway_method.proxy_root.http_method
#
#  integration_http_method = "GET"
#  type                    = "AWS_PROXY"
#  uri                     = aws_lambda_function.lambda.invoke_arn
#}
#
#resource "aws_api_gateway_deployment" "default" {
#  depends_on = [
#    aws_api_gateway_integration.lambda,
#    aws_api_gateway_integration.lambda_root,
#  ]
#
#  rest_api_id = aws_api_gateway_rest_api.restart.id
#  stage_name  = "dev"
#}

#resource "aws_lambda_permission" "apigw" {
#  statement_id  = "AllowAPIGatewayInvoke"
#  action        = "lambda:InvokeFunction"
#  function_name = aws_lambda_function.lambda.function_name
#  principal     = "apigateway.amazonaws.com"
#
#  # The /*/* portion grants access from any method on any resource
#  # within the API Gateway "REST API".
#  source_arn = "${aws_api_gateway_rest_api.restart.execution_arn}/*/*"
#}
