<?php

//SMTP Settings
function wpse8170_phpmailer_init(\PHPMailer\PHPMailer\PHPMailer $phpmailer) {
  $phpmailer->isMail();
  $phpmailer->SMTPDebug = 0;
  $phpmailer->Host = 'email-smtp.us-east-1.amazonaws.com';
  $phpmailer->Port = 25;
  $phpmailer->Username = 'AKIA3RT27CBCSK4EHZHE';
  $phpmailer->Password = 'BB4oqOSu/t38ABUQJC6INM2sxdT/B2gH0cuCRecfUaqX';
  $phpmailer->SMTPAuth = true;
  $phpmailer->SMTPSecure = 'tls';
}

add_action('phpmailer_init', 'wpse8170_phpmailer_init');
