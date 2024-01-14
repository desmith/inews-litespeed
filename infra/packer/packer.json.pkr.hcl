# See https://www.packer.io/docs/templates/hcl_templates/blocks/packer for more info
packer {
    required_plugins {
        amazon = {
            source  = "github.com/hashicorp/amazon"
            version = "~> 1"
        }
        ansible = {
            source  = "github.com/hashicorp/ansible"
            version = "~> 1"
        }
    }
}

# https://www.packer.io/docs/templates/hcl_templates/variables#type-constraints for more info.
variable "ansible_bin_path" {
    type    = string
    default = "./.venv/bin"
}

variable "aws_region" {
    type    = string
    default = "us-east-1"
}

variable "ami_name" {
    type    = string
    default = "packer-ami"
}

variable "app_name" {
    type    = string
}

variable "component" {
    type    = string
    default = "webserver"
}

variable "env" {
    type    = string
    default = "dev"
}

variable "iam_instance_profile" {
    type    = string
    default = "WebServerRole"
}

variable "instance_type" {
    type    = string
    default = "t3.2xlarge"
}

variable "security_group_ids" {
    type    = list(string)
    default = [
        "sg-01432a22139cc83e7",
        "sg-006a133876cdd75fe",
        "sg-02fb8ee3c8ace0441"
    ]
}

variable "source_ami" {
    type = string
}

variable "subnet_id" {
    type = string
}

variable "vpc_id" {
    type = string
}

# source blocks are generated from your builders; a source can be referenced in
# build blocks. A build block runs provisioner and post-processors on a
# source. Read the documentation for source blocks here:
# https://www.packer.io/docs/templates/hcl_templates/blocks/source
# could not parse template for following block: "template: hcl2_upgrade:2: bad character U+0060 '`'"

source "amazon-ebs" "packer-ebs" {
    ami_name             = "${var.ami_name}-{{isotime \"20060102030405\"}}"
    iam_instance_profile = var.iam_instance_profile
    instance_type        = var.instance_type
    launch_block_device_mappings {
        delete_on_termination = true
        device_name           = "/dev/sda1"
        encrypted             = false
        volume_size           = 12
        volume_type           = "gp3"
    }
    region   = var.aws_region
    run_tags = {
        Name = "packer-${var.ami_name}"
        app  = var.app_name
        env  = var.env
    }

    security_group_ids = var.security_group_ids
    snapshot_tags      = {
        ec2-cleanup = "False"
    }

    associate_public_ip_address = true
    source_ami                  = var.source_ami
    ssh_pty                     = true
    ssh_username                = "ec2-user"
    temporary_key_pair_type     = "ed25519"
    ssh_agent_auth              = false
    subnet_id                   = var.subnet_id

    # IMDSv2_support
    metadata_options {
        http_endpoint               = "enabled"
        http_put_response_hop_limit = "1"
        http_tokens                 = "required"
    }

    tags = {
        CreationDate = "{{isotime \"20060102 15:04:05 MST\"}}"
        Name         = "${var.app_name}-${var.component}-${var.env}"
        app          = var.app_name
        component    = var.component
        env          = var.env
        ec2-cleanup  = "False"
        created_by   = "packer"
    }
    vpc_id = var.vpc_id
}

# a build block invokes sources and runs provisioning steps on them. The
# documentation for build blocks can be found here:
# https://www.packer.io/docs/templates/hcl_templates/blocks/build
build {
    sources = ["source.amazon-ebs.packer-ebs"]

    provisioner "file" {
        source      = "./files/perflog"
        destination = "/tmp/perflog"
    }
    provisioner "file" {
        source      = "./files/monitor"
        destination = "/tmp/monitor"
    }
    provisioner "file" {
        source      = "./files/crontab.src"
        destination = "/tmp/crontab.src"
    }

    provisioner "shell" {
        execute_command = "{{ .Vars }} sudo -E -S bash '{{ .Path }}'"
        script          = "./scripts/10-install_os"
    }

    provisioner "shell" {
        execute_command = "{{ .Vars }} sudo -E -S bash '{{ .Path }}' ${var.env}"
        script          = "./scripts/12-install-fstab"
    }

    provisioner "shell" {
        execute_command = "{{ .Vars }} sudo -E -S bash '{{ .Path }}'"
        script          = "./scripts/15-install-ssh-keys"
    }

    provisioner "shell" {
        execute_command = "{{ .Vars }} sudo -E -S bash '{{ .Path }}'"
        script          = "./scripts/20-install-php"
    }

    provisioner "shell" {
        execute_command = "{{ .Vars }} sudo -E -S bash '{{ .Path }}'"
        script          = "./scripts/30-configure-lsws"
    }

    provisioner "file" {
        source      = "./files/aws_config"
        destination = "/tmp/aws_config"
    }
    provisioner "file" {
        source      = "./files/aws_credentials"
        destination = "/tmp/aws_credentials"
    }
    provisioner "shell" {
        execute_command = "{{ .Vars }} sudo -E -S bash '{{ .Path }}'"
        script          = "./scripts/40-install-awscli"
    }

    provisioner "file" {
        source      = "./files/cwagent_config.json"
        destination = "/tmp/amazon-cloudwatch-agent.json"
    }
    provisioner "shell" {
        execute_command = "{{ .Vars }} sudo -E -S bash '{{ .Path }}'"
        script          = "./scripts/50-install-cwagent"
    }

    provisioner "shell" {
        execute_command = "{{ .Vars }} sudo -E -S bash '{{ .Path }}'"
        script          = "./scripts/70-install-extras"
    }

    provisioner "shell" {
        script          = "./scripts/80-install-optional"
    }

    #    provisioner "shell" {
    #        script = "./scripts/config-ecr-credentials-helper.sh"
    #    }

    #    provisioner "ansible" {
    #        command        = "${var.ansible_bin_path}/ansible-playbook"
    #        galaxy_command = "${var.ansible_bin_path}/ansible-galaxy"
    #        extra_arguments = ["-vv"]
    #        galaxy_file     = "ansible_requirements.yml"
    #        playbook_file   = "ansible_playbook.yml"
    #        use_proxy      = false
    #    }

    provisioner "shell" {
        inline = ["aws --version"]
    }

    post-processor "manifest" {
        output     = "manifest.json"
        strip_path = true
    }
    post-processor "shell-local" {
        inline = ["jq -r .builds[].artifact_id manifest.json | awk -F : '{print $2}' > ami.txt"]
    }

    post-processor "shell-local" {
        execute_command = ["bash", "-c", "{{.Vars}} {{.Script}} ${var.env}"]
        script = "./scripts/99-post-build"
    }
}
