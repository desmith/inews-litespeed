<?php

/**
 * phpMyAdmin configuration file, you can use it as base for the manual
 * configuration. For easier setup you can use "setup/".
 *
 * All directives are explained in Documentation.html and on phpMyAdmin
 * wiki <http://wiki.phpmyadmin.net>.
 */

/*
 * This is needed for cookie based authentication to encrypt password in cookie
 */
$cfg['blowfish_secret'] = 'WYPUq7ns0G1VUDDLe6xPHXxMcRhRpAfQ'; /* YOU MUST FILL IN THIS FOR COOKIE AUTH! */

//$cfg['ForceSSL'] = 'false';
$cfg['ForceSSL'] = 'true';
$cfg['ExecTimeLimit'] = 0;


/**
 * Server(s) configuration
 */
$i = 0;

// The $cfg['Servers'] array starts with $cfg['Servers'][1].  Do not use
// $cfg['Servers'][0]. You can disable a server config entry by setting host
// to ''. If you want more than one server, just copy following section
// (including $i incrementation) several times. There is no need to define
// full server array, just define values you need to change.
$i++;

//$cfg['Servers'][$i]['ssl']         = TRUE;
//$cfg['Servers'][$i]['ssl_cert']        =  '/etc/ssl/certs/rds-combined-ca-bundle.pem';

/***
$cfg['Servers'][$i]['ssl_key'] and $cfg['Servers'][$i]['ssl_cert']
This is used for authentication of client to the server.
$cfg['Servers'][$i]['ssl_ca'] and $cfg['Servers'][$i]['ssl_ca_path']
The certificate authorities you trust for server certificates. This is used to ensure that you are talking to a trusted server.
**/

$cfg['Servers'][$i]['host']          = '${db_host}'; // MySQL hostname or IP address
$cfg['Servers'][$i]['port']          = '3306';          // MySQL port - leave blank for default port
$cfg['Servers'][$i]['socket']        = '';          // Path to the socket - leave blank for default socket
$cfg['Servers'][$i]['connect_type']  = 'tcp';       // How to connect to MySQL server ('tcp' or 'socket')
$cfg['Servers'][$i]['extension']     = 'mysqli';    // The php MySQL extension to use ('mysql' or 'mysqli')
$cfg['Servers'][$i]['compress']      = FALSE;       // Use compressed protocol for the MySQL connection

$cfg['Servers'][$i]['controluser']   = '${pma_username}';
$cfg['Servers'][$i]['controlpass']   = '${pma_password}';

$cfg['Servers'][$i]['auth_type']     = 'cookie';    // Authentication method (config, http or cookie based)?
$cfg['Servers'][$i]['user']          = '';          // MySQL user
$cfg['Servers'][$i]['password']      = '';          // MySQL password (only needed
                                                    // with 'config' auth_type)
$cfg['Servers'][$i]['only_db']       = '';          // If set to a db-name, only
                                                    // this db is displayed in left frame
                                                    // It may also be an array of db-names, where sorting order is relevant.
//$cfg['Servers'][$i]['hide_db']       = '';
$cfg['Servers'][$i]['hide_db']       = 'information_schema|performance_schema|mysql|phpmyadmin';          // Database name to be hidden from listings
$cfg['Servers'][$i]['verbose']       = '';          // Verbose name for this host - leave blank to show the hostname

/* Storage database and tables */
$cfg['Servers'][$i]['pmadb'] = 'phpmyadmin';
$cfg['Servers'][$i]['bookmarktable'] = 'pma__bookmark';
$cfg['Servers'][$i]['relation'] = 'pma__relation';
$cfg['Servers'][$i]['table_info'] = 'pma__table_info';
$cfg['Servers'][$i]['table_coords'] = 'pma__table_coords';
$cfg['Servers'][$i]['pdf_pages'] = 'pma__pdf_pages';
$cfg['Servers'][$i]['column_info'] = 'pma__column_info';
$cfg['Servers'][$i]['history'] = 'pma__history';
$cfg['Servers'][$i]['table_uiprefs'] = 'pma__table_uiprefs';
$cfg['Servers'][$i]['tracking'] = 'pma__tracking';
$cfg['Servers'][$i]['userconfig'] = 'pma__userconfig';
$cfg['Servers'][$i]['recent'] = 'pma__recent';
$cfg['Servers'][$i]['favorite'] = 'pma__favorite';
$cfg['Servers'][$i]['users'] = 'pma__users';
$cfg['Servers'][$i]['usergroups'] = 'pma__usergroups';
$cfg['Servers'][$i]['navigationhiding'] = 'pma__navigationhiding';
$cfg['Servers'][$i]['savedsearches'] = 'pma__savedsearches';
$cfg['Servers'][$i]['central_columns'] = 'pma__central_columns';
$cfg['Servers'][$i]['designer_settings'] = 'pma__designer_settings';
$cfg['Servers'][$i]['export_templates'] = 'pma__export_templates';

$cfg['Servers'][$i]['verbose_check'] = TRUE;


$cfg['Servers'][$i]['AllowRoot']     		= TRUE;
$cfg['Servers'][$i]['AllowDeny']['order']    	= '';
$cfg['Servers'][$i]['AllowDeny']['rules']     	= array();
$cfg['Servers'][$i]['AllowNoPassword']        	= FALSE;
$cfg['Servers'][$i]['designer_coords']         	= '';
$cfg['Servers'][$i]['bs_garbage_threshold']    	= 50;
$cfg['Servers'][$i]['bs_repository_threshold'] 	= '32M';
$cfg['Servers'][$i]['bs_temp_blob_timeout'] 	= 600;
$cfg['Servers'][$i]['bs_temp_log_threshold'] 	= '32M';


/*
 * End of servers configuration
 */

/*
 * Directories for saving/loading files from server
 */
//$cfg['UploadDir'] = '/var/lib/phpMyAdmin/upload';
//$cfg['SaveDir']   = '/var/lib/phpMyAdmin/save';

/*
 * Disable the default warning that is displayed on the DB Details Structure
 * page if any of the required Tables for the relation features is not found
 */
$cfg['PmaNoRelation_DisableWarning'] = FALSE;

/*
 * phpMyAdmin 4.4.x is no longer maintained by upstream, but security fixes
 * are still backported by downstream.
 */
$cfg['VersionCheck'] = TRUE;
?>
