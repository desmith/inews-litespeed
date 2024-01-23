<?php

const AUTOLOAD_PHP = '/vendor/autoload.php';
if(file_exists(__DIR__ . AUTOLOAD_PHP)) {
    require_once __DIR__ . AUTOLOAD_PHP;
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}
if(file_exists(dirname(__DIR__) . AUTOLOAD_PHP )) {
    require_once dirname(__DIR__) . AUTOLOAD_PHP;
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

const WP_CACHE = true;
const FS_METHOD = 'direct';
const DISABLE_WP_CRON = true;

/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

define( 'DB_NAME', getenv('DB_NAME'));
define( 'DB_USER', getenv('DB_USER'));
define( 'DB_PASSWORD', getenv('DB_PASSWORD'));
define( 'DB_HOST', getenv('DB_HOST'));

/** Database charset to use in creating database tables. */
const DB_CHARSET = 'utf8mb4';

/** The database collate type. Don't change this if in doubt. */
const DB_COLLATE = 'utf8mb4_unicode_ci';

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */

define( 'AUTH_KEY',  getenv('AUTH_KEY'));
define( 'SECURE_AUTH_KEY',  getenv('SECURE_AUTH_KEY'));
define( 'LOGGED_IN_KEY',  getenv('LOGGED_IN_KEY'));
define( 'NONCE_KEY',  getenv('NONCE_KEY'));
define( 'AUTH_SALT',  getenv('AUTH_SALT'));
define( 'SECURE_AUTH_SALT',  getenv('SECURE_AUTH_SALT'));
define( 'LOGGED_IN_SALT',  getenv('LOGGED_IN_SALT'));
define( 'NONCE_SALT',  getenv('NONCE_SALT'));

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'ick_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */


//const WP_DEBUG = true;
//const WP_DEBUG_LOG = true;
const WP_DEBUG_DISPLAY = false;


/* Add any custom values between this line and the "stop editing" line. */

//const FORCE_SSL_ADMIN = false;
//const FORCE_SSL_LOGIN = false;

if (isset($_SERVER['HTTP_HOST'])) {
	define('WP_SITEURL', 'https://' . $_SERVER['HTTP_HOST'] . '/');
	define('WP_HOME', 'https://' . $_SERVER['HTTP_HOST'] . '/');
}


//if (isset($_SERVER['HTTP_CLOUDFRONT_FORWARDED_PROTO']) && $_SERVER['HTTP_CLOUDFRONT_FORWARDED_PROTO'] === 'https') { $_SERVER['HTTPS'] = 'on'; }
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'){ $_SERVER['HTTPS']='on'; }

/* Offload Media to Cloudflare plugin */


/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';