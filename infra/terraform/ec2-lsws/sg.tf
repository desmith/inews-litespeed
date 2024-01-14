
data "aws_security_group" "admin" {
  name = "AdminSG"
}

data "aws_security_group" "webapp" {
  name = "WebApp-LightSpeedSG"
}

data "aws_security_group" "waf" {
  name = "WordFenceSG"
}
