#output "ec2_instance_ip" {
#  value = aws_instance.inews.private_ip
#}

output "route53_zone_id" {
  value = data.aws_route53_zone.inews.zone_id
}

output "ec2_instance_id" {
  value = data.aws_instances.inews[*].ids
}

output "ec2_instance_ip" {
  value = data.aws_instances.inews[*].private_ips
}
