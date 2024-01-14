app_name                  = "inews"
component                 = "litespeed"
keypair_name              = "IC-News"
instance_type             = "t3.medium"
ssm_param_ami_id          = "/inews/infra/prod/litespeed/ami_id"
extra_security_group_ids  = ["sg-006a133876cdd75fe"]
elb_listener_priority     = 14
target_group_host_headers = [
    "prod.iskconnews.org",
    "iskconnews.org",
    "www.iskconnews.org"
]
alb_certificate_arn = "arn:aws:acm:us-east-1:793753096261:certificate/c9bb28af-b8d0-44ba-8fac-ed3642605809"
alb_arn             = "arn:aws:elasticloadbalancing:us-east-1:793753096261:loadbalancer/app/ICG-ELB/ed8f2ac6e693c7b2"
