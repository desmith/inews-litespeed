#data "aws_route53_zone" "inews" {
#  name = var.route53_zone_name
#}
#
#data "aws_acm_certificate" "issued" {
#  domain   = var.route53_zone_name
#  statuses = ["ISSUED"]
#}
#
#resource "aws_apigatewayv2_domain_name" "restart" {
#  domain_name = "ec2-dev.${var.route53_zone_name}"
#
#  domain_name_configuration {
#    certificate_arn = data.aws_acm_certificate.issued.arn
#    endpoint_type   = "REGIONAL"
#    security_policy = "TLS_1_2"
#  }
#}
#
#resource "aws_route53_record" "restart" {
#  name    = aws_apigatewayv2_domain_name.restart.domain_name
#  type    = "A"
#  zone_id = data.aws_route53_zone.inews.zone_id
#
#  alias {
#    name                   = aws_apigatewayv2_domain_name.restart.domain_name_configuration[0].target_domain_name
#    zone_id                = aws_apigatewayv2_domain_name.restart.domain_name_configuration[0].hosted_zone_id
#    evaluate_target_health = false
#  }
#}
