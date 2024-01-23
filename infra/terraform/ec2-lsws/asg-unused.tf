#data "aws_subnets" "private" {
#  filter {
#    name   = "vpc-id"
#    values = [var.vpc_id]
#  }
#
#  tags = {
#    network = "private"
#  }
#}
#
#
#resource "aws_iam_instance_profile" "inews-infra" {
#  name = var.app
#  role = var.ec2_role
#}
#
#resource "aws_launch_template" "inews-infra" {
#  name_prefix            = "${var.app}-${var.component}-"
#  image_id               = data.aws_ami.inews-infra.image_id
#  vpc_security_group_ids = [
#    data.aws_security_group.admin.id,
#    data.aws_security_group.webapp.id
#  ]
#
#
#  iam_instance_profile {
#    name = aws_iam_instance_profile.inews-infra.name
#  }
#
#  block_device_mappings {
#    device_name = "/dev/xvda"
#
#    ebs {
#      volume_size = 12
#      volume_type = "gp3"
#      iops        = 3000
#
#    }
#  }
#}
#
#resource "aws_autoscaling_group" "inews-infra" {
#  name                      = "${var.app}-${var.component}-${aws_launch_template.inews-infra.latest_version}-${var.env}-as"
#  desired_capacity          = 1
#  max_size                  = 1
#  min_size                  = 0
#  termination_policies      = ["OldestLaunchConfiguration"]
#  health_check_grace_period = 600
#  health_check_type         = "ELB"
#  vpc_zone_identifier        = data.aws_subnets.private.ids
#  target_group_arns         = [data.aws_lb_target_group.inews-infra.arn]
#  force_delete = true
#
#  launch_template {
#    id      = aws_launch_template.inews-infra.id
#    version = aws_launch_template.inews-infra.latest_version
#  }
#
#  tag {
#    key                 = "Name"
#    value               = local.instance_name
#    propagate_at_launch = true
#  }
#
#  tag {
#    key                 = "app"
#    value               = var.app
#    propagate_at_launch = true
#  }
#
#  tag {
#    key                 = "env"
#    value               = var.env
#    propagate_at_launch = true
#  }
#
#  tag {
#    key                 = "Terraform"
#    value               = true
#    propagate_at_launch = true
#  }
#
#  lifecycle {
#    create_before_destroy = true
#  }
#
#  timeouts {
#    delete = "20m"
#  }
#}
#
#resource "aws_autoscaling_schedule" "scale_down" {
#  count                  = var.enable_autoscaling_schedule ? 1 : 0
#  scheduled_action_name  = "scale_down"
#  max_size               = 0
#  desired_capacity       = 0
#  recurrence             = var.as_schedule_down
#  autoscaling_group_name = aws_autoscaling_group.inews-infra.name
#}
#
#resource "aws_autoscaling_schedule" "scale_up" {
#  count                  = var.enable_autoscaling_schedule ? 1 : 0
#  scheduled_action_name  = "scale_up"
#  max_size               = 1
#  desired_capacity       = 1
#  recurrence             = var.as_schedule_up
#  autoscaling_group_name = aws_autoscaling_group.inews-infra.name
#}
#
#resource "aws_cloudwatch_metric_alarm" "infra_cpu_80_alarm" {
#  alarm_name          = "${aws_autoscaling_group.inews-infra.name}-80-cpu-alarm"
#  comparison_operator = "GreaterThanOrEqualToThreshold"
#  evaluation_periods  = "2"
#  metric_name         = "CPUUtilization"
#  namespace           = "AWS/EC2"
#  period              = "120"
#  statistic           = "Average"
#  threshold           = "80"
#
#  dimensions = {
#    AutoScalingGroupName = aws_autoscaling_group.inews-infra.name
#  }
#
#  alarm_description = "This metric monitors ec2 cpu utilization"
#  alarm_actions     = [var.ec2_alarm_topic]
#}
#
#resource "aws_cloudwatch_metric_alarm" "infra_disk_85_alarm" {
#  alarm_name          = "${aws_autoscaling_group.inews-infra.name}-85-disk-alarm"
#  comparison_operator = "GreaterThanOrEqualToThreshold"
#  evaluation_periods  = "2"
#  metric_name         = "disk_used_percent"
#  namespace           = "CWAgent"
#  period              = "120"
#  statistic           = "Average"
#  threshold           = "85"
#
#  dimensions = {
#    AutoScalingGroupName = aws_autoscaling_group.inews-infra.name
#  }
#
#  alarm_description = "This metric monitors ec2 disk utilization"
#  alarm_actions     = [var.ec2_alarm_topic]
#}
#
#resource "aws_cloudwatch_metric_alarm" "infra_memory_95_alarm" {
#  alarm_name          = "${aws_autoscaling_group.inews-infra.name}-95-memory-alarm"
#  comparison_operator = "GreaterThanOrEqualToThreshold"
#  evaluation_periods  = "2"
#  metric_name         = "mem_used_percent"
#  namespace           = "CWAgent"
#  period              = "120"
#  statistic           = "Average"
#  threshold           = "95"
#
#  dimensions = {
#    AutoScalingGroupName = aws_autoscaling_group.inews-infra.name
#  }
#
#  alarm_description = "This metric monitors ec2 memory utilization"
#  alarm_actions     = [var.ec2_alarm_topic]
#}
