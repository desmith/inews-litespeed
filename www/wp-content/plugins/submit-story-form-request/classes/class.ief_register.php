<?php
/**
 * Custom Submit Story Form Request Installation.
 *
 */
class ief_register {

	protected static $instance;

    //Initialization of the instance
    public static function init()
    {
        is_null( self::$instance ) AND self::$instance = new self;
        return self::$instance;
    }

    //Activation of the plugin
    function ief_install()
    {
        ob_start();
        global $wpdb;
        $table_name = $wpdb->prefix . 'story_requests';
        $charset_collate = $wpdb->get_charset_collate();
        $sql ="CREATE TABLE $table_name (
                id INT NOT NULL AUTO_INCREMENT,
                `wb_fullname` varchar(150) DEFAULT NULL,
				`wb_email` varchar(150) DEFAULT NULL,
                `wb_phone` varchar(150) DEFAULT NULL,
				`wb_location` varchar(150) DEFAULT NULL,
				`wb_message` varchar(255) DEFAULT NULL,
				`cv` varchar(255) DEFAULT NULL,
                `ip_addr` varchar(255) DEFAULT NULL,       
                `added_on` DATETIME NOT NULL,            
                `updated_on` DATETIME NOT NULL,            
                `flag` TINYINT(4) DEFAULT 0 NOT NULL COMMENT '0 => Active, 1 => Inactive, 2 => Deleted',            
                PRIMARY KEY (id) )";
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
        return ob_get_clean();
    }

    //Deactivation of the plugin
    function ief_uninstall()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'story_requests';
        $sql = "DROP TABLE IF EXISTS $table_name";
        $wpdb->query($sql);
    }
}
?>