env                         = "dev"
inews_ami_id_ssm_param      = "/infra/inews/AMI_KEY_DEV"
instance_type               = "t3.small"
enable_autoscaling_schedule = true
# times in UTC
as_schedule_down = "0 4 * * *"
as_schedule_up = "0 13 * * FRI-SUN"
