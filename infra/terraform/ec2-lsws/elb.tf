# create a target group
resource "aws_lb_target_group" "inews" {
  name     = local.target_group_name
  port     = 80
  protocol = "HTTP"
  vpc_id   = var.vpc_id
}

# add the newly-created ec2 instance to the target group
resource "aws_lb_target_group_attachment" "target_group_attachment" {
  target_group_arn = aws_lb_target_group.inews.arn
  target_id        = aws_instance.inews.id
  port             = 80
}

# add the target group to the load balancer
resource "aws_lb_listener_rule" "listener_rule" {
  listener_arn = var.https_listener_arn
  priority     = var.elb_listener_priority

  action {
    type             = "forward"
    target_group_arn = aws_lb_target_group.inews.arn
  }

  condition {
    host_header {
      values = var.target_group_host_headers
    }
  }

  tags = {
    Name = local.target_group_name
  }

}
