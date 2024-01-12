resource "aws_sns_topic" "auto_restart" {
  name = "ec2-auto-restart-topic"
}

resource "aws_sns_topic_subscription" "email-target" {
  topic_arn = aws_sns_topic.auto_restart.arn
  protocol  = "email"
  endpoint  = var.email_recipients
}
