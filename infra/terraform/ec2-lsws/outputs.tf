output "instance_id" {
  value = aws_instance.inews.id
}

output "instance_private_ip" {
  value = aws_instance.inews.private_ip
}

output "instance_public_ipv4" {
  value = [aws_instance.inews.public_ip]
}

output "instance_public_ipv6" {
  value = [aws_instance.inews.ipv6_addresses]
}

output "instance_public_dns" {
  value = [aws_instance.inews.public_dns]
}

#output "elb_dns" {
#  value = data.aws_lb.icg.dns_name
#}
