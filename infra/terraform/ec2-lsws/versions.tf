terraform {
  required_version = ">= 1.0"

  backend "s3" {
    # Keep this empty
  }

  required_providers {
    aws = {
      source  = "hashicorp/aws"
      version = ">= 4.4"
    }
  }
}

provider "aws" {
  region = "us-east-1"
}
