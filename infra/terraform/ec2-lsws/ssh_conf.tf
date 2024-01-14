data "aws_instances" "inews" {
  instance_tags = {
    Name = local.instance_name
  }
  instance_state_names = ["running", "pending"]

  #  filter = {
  #    name   = "tag:Name"
  #    values = [local.instance_name]
  #  }

}

resource "local_file" "ssh_config" {
  #  for_each = toset(data.aws_instances.inews.private_ips)
  count   = length(data.aws_instances.inews.private_ips)
  content = templatefile("./files/ssh_config_local.tftpl",
    {
      count               = count.index
      env                 = var.env
      private_ip4_address = data.aws_instances.inews.private_ips[count.index]
      private_key_file     = "${var.keypair_name}.pem"
    })
  filename        = "${var.ssh_conf_dir}/${local.instance_name}-${count.index}.ssh"
  file_permission = "0600"
}
