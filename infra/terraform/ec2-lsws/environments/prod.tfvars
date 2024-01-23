app_name                    = "inews"
component                   = "litespeed"
keypair_name                = "IC-News"
instance_type               = "t3.medium"
ssm_param_ami_id            = "/inews/infra/prod/litespeed/ami_id"
subnet_id                   = "subnet-5b4d9954"
extra_security_group_ids    = ["sg-006a133876cdd75fe"]
ec2_eip_name                = "inews-lsws-prod"
elb_listener_priority       = 13
elb_listener_priority_admin = 14
target_group_host_headers   = [
    "iskconnews.org",
    "prod.iskconnews.org",
    "www.iskconnews.org"
]
target_group_host_headers_admin = [
    "lsws.iskconnews.org"
]
alb_certificate_arn = "arn:aws:acm:us-east-1:793753096261:certificate/c9bb28af-b8d0-44ba-8fac-ed3642605809"
alb_arn             = "arn:aws:elasticloadbalancing:us-east-1:793753096261:loadbalancer/app/ICG-ELB/ed8f2ac6e693c7b2"
