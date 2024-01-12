data "aws_sns_topic" "auto_restart" {
  name = "ec2-auto-restart-topic"
}
