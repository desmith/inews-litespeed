name             = "ec2_restart"
app              = "inews"
component        = "infra"
env              = "prod"
//schedule         = "cron(0 */6 ? * * *)" # every 4 hours
schedule         = "rate(6 hours)" # every 4 hours
lambda_runtime   = "python3.12"
lambda_handler   = "restart_instance.main"
