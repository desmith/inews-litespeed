app_name = "inews"
component = "litespeed"
keypair_name       = "IC-News"
instance_type      = "t3.small"
ssm_param_ami_id   = "/inews/infra/dev/litespeed/ami_id"
elb_listener_priority = 15
target_group_host_headers = [
    "dev.iskconnews.org",
    "infra-dev.iskconnews.org",
    "pma-infra.iskconnews.org"
]
