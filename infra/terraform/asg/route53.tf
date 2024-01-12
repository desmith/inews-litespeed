data "aws_route53_zone" "inews" {
  name = var.domain
}

#resource "aws_route53_record" "inews" {
#  zone_id = data.aws_route53_zone.inews.zone_id
#  name    = "${var.route53_subdomain}.${var.domain}"
#  type    = "A"
#  ttl     = 300
#  records = [aws_eip.lb.public_ip]
#}
