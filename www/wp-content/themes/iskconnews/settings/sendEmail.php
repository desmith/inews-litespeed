<?php
require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))) . DIRECTORY_SEPARATOR . 'wp-load.php');

Class SendEmail{

	public function __construct(){}

	public function template($body){
		$header = get_option('email_header');
		$footer = get_option('email_footer');
		$link = get_stylesheet_directory_uri().'/assets/img';
		$header = str_replace('{url}', $link, $header);
		$footer = str_replace('{url}', $link, $footer);
		return `<!DOCTYPE html>
		<html>
		    <head>
		        <title>Mail Template</title>
		        <meta charset="utf-8">
		        <style type="text/css">
		            body{margin: 0px; padding: 0px;}
		            table, table tr{vertical-align: top;border-spacing: 0;}
		            table tr{margin: 0px;padding: 0px;border-spacing: 0;}
		            table tr td, table tr th{margin: 0px;}
		            th, td{padding: 0px; vertical-align: middle;}
		            img{margin: 0px;padding: 0px;}
		        </style>
		    </head>
		    <body>
		        <table style="width: 650px; background-color: #ffffff; margin: 0px; padding: 0px; margin:auto;" border="0" cellspacing="0" cellpadding="0">
		            <tbody>
		                <!-- HEADER -->
			            	`.$header.`
			            <!-- HEADER -->
		                <!-- BODY -->
			               	`.$body.`
			                <!-- BODY -->
		                <!-- FOOTER -->
		                `.$footer.`
		                <!-- FOOTER -->
		            </tbody>
		         </table>        
		    </body>
		</html>`;

	}
}
