resource "aws_ec2_instance_state" "instance" {
  instance_id = aws_instance.instance.id
}

