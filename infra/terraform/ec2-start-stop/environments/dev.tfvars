name             = "ec2-lsws-dev-start-stop"
stop_schedule    = "cron(45 */6 * * ? *)" # every 4 hours
email_recipients = "devaprastha@iskcon.org"
email_sender     = "devaprastha@iskcon.org"
lambda_handler   = "start_stop.handler"
sns_topic_name   = "ec2-auto-restart-topic"
route53_zone_name = "iskconnews.net"
