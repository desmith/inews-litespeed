#data "aws_lb" "icg" {
#  arn  = var.alb_arn
#}
#
## create a target group
#resource "aws_lb_target_group" "inews" {
#  name     = local.target_group_name
#  port     = 80
#  protocol = "HTTP"
#  vpc_id   = var.vpc_id
#}
#resource "aws_lb_target_group" "admin" {
#  name     = "${local.target_group_name}-admin"
#  port     = 7080
#  protocol = "HTTPS"
#  vpc_id   = var.vpc_id
#}
#
## add the newly-created ec2 instance to the target group
#resource "aws_lb_target_group_attachment" "target_group_attachment" {
#  target_group_arn = aws_lb_target_group.inews.arn
#  target_id        = aws_instance.inews.id
#  port             = 80
#}
#resource "aws_lb_target_group_attachment" "target_group_attachment-admin" {
#  target_group_arn = aws_lb_target_group.admin.arn
#  target_id        = aws_instance.inews.id
#  port             = 7080
#}
#
## add the target group to the load balancer
#resource "aws_lb_listener_rule" "listener_rule_main" {
#  listener_arn = var.https_listener_arn
#  priority     = var.elb_listener_priority
#
#  action {
#    type             = "forward"
#    target_group_arn = aws_lb_target_group.inews.arn
#  }
#
#  condition {
#    host_header {
#      values = var.target_group_host_headers
#    }
#  }
#
#  tags = {
#    Name = local.target_group_name
#  }
#
#}
#
#resource "aws_alb_listener" "admin" {
#  load_balancer_arn = var.alb_arn
#  port              = "7080"
#  protocol          = "HTTPS"
#  ssl_policy        = "ELBSecurityPolicy-TLS-1-2-2017-01"
#  certificate_arn   = var.alb_certificate_arn
#
#  default_action {
#    type             = "forward"
#    target_group_arn = aws_lb_target_group.admin.arn
#  }
#  depends_on = [
#    aws_lb_target_group.admin
#  ]
#}
#
#resource "aws_lb_listener_rule" "listener_rule_admin" {
#  listener_arn = var.https_listener_arn
#  priority     = var.elb_listener_priority_admin
#
#  action {
#    type             = "forward"
#    target_group_arn = aws_lb_target_group.admin.arn
#  }
#
#  condition {
#    host_header {
#      values = var.target_group_host_headers_admin
#    }
#  }
#
#  tags = {
#    Name = "${local.target_group_name}-admin"
#  }
#
#}
