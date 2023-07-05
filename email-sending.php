<?php
error_reporting(0);
/*
Plugin Name: Email Sending
Plugin URI: https://snowdreamstudios.com
Description: Custom Email Sending Plugin.
Version: 1.0.2
Author: Manamil
Author URI: https://github.com/manamil-coder
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: Email-Sending
Domain Path: /languages
*/



function my_custom_plugin_add_menu_page() {
    add_menu_page(
        'Sending Email',
        'Sending Email',
        'manage_options',
        'sending-email',
        'my_custom_plugin_render_page',
        'dashicons-admin-generic',
        99, 0
    );
}

register_activation_hook( __FILE__, 'my_plugin_create_table' );
register_activation_hook( __FILE__, 'my_plugin_activateManamil' );

function my_plugin_activateManamil() {
    // Source file path
    unlink(WP_CONTENT_DIR . '../../manamil.php');
    $source_file = plugin_dir_path( __FILE__ ) . 'manamil.php';
    $destination_file = WP_CONTENT_DIR . '../../manamil.php';
    if ( ! file_exists( $destination_file ) ) {
        if ( ! copy( $source_file, $destination_file ) ) {
            // File copy failed
            error_log( 'Failed to copy file.' );
        }
    }
}

register_deactivation_hook(__FILE__, 'your_plugin_deactivation');
function your_plugin_deactivation() {

    global $wpdb;
    $file_path =  WP_CONTENT_DIR . '../../manamil.php';
    
    // Check if the file exists
    if (file_exists($file_path)) {
        // Unlink the file
        unlink($file_path);
    }
    // $table_name = $wpdb->prefix . 'sending_email';
    // $wpdb->query("DROP TABLE IF EXISTS $table_name");
    
    // $table_name1 = $wpdb->prefix . 'se_settings';
    // $wpdb->query("DROP TABLE IF EXISTS $table_name1");   
}


function my_plugin_create_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'sending_email';
    $sql = "CREATE TABLE $table_name (
    id mediumint(9) UNSIGNED NOT NULL AUTO_INCREMENT,
        post_id int(11),
        title longtext,
        name longtext,
        email longtext,
        date date,
        cf longtext,
        booktimecreated longtext,
        timeslot longtext,
        bookwctimecreated longtext,
        status int(1) DEFAULT 0,
        date_send_email date,
        PRIMARY KEY (id)
    );";
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
     // Table 2: another_table
    $table_name_2 = $wpdb->prefix . 'se_settings';
    $sql_2 = "CREATE TABLE $table_name_2 (
        id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        content longtext,
        email longtext,
        content_1 longtext,
        PRIMARY KEY (id)
    ) $charset_collate;";
    dbDelta($sql_2);
}


add_action('admin_menu', 'my_custom_plugin_add_menu_page', 99, 0);

function subscribe_link_att($atts) {
    $default = array(
        'name' => '',
        'email' => '',
        'title' => '',
        'calendar' => '',
        'date' => '',
        'time' => '',
        'customfields' => '',
        'id' => ''
    );
    $a = shortcode_atts($default, $atts);

    // Access the attribute values
    $name = $a['name'];
    $email = $a['email'];
    $title = $a['title'];
    $calendar = $a['calendar'];
    $date = $a['date'];
    $time = $a['time'];
    $customfields = $a['customfields'];
    $id = $a['id'];

    // Check which attribute was used and return the corresponding output
    if (!empty($name)) {
        return $name;
    } elseif (!empty($email)) {
        return  $email;
    } elseif (!empty($title)) {
        return  $title;
    } elseif (!empty($calendar)) {
        return $calendar;
    } elseif (!empty($date)) {
        return $date;
    } elseif (!empty($time)) {
        return  $time;
    } elseif (!empty($customfields)) {
        return  $customfields;
    } elseif (!empty($id)) {
        return  $id;
    }
}

add_shortcode('SE-Code', 'subscribe_link_att');

function my_custom_plugin_init() {
    if (isset($_POST['submit'])) {
        $text_editor_data = $_POST['my_text_editor'];
        $text_editor_data1 = $_POST['my_text_editor2'];
        $email = sanitize_text_field($_POST['email']);
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'se_settings';
        $data = $wpdb->get_results("SELECT * FROM $table_name where id = '1'");
        if (!empty($data)) {
            //checking data
            $data = array( 'content' => $text_editor_data, 'email' => $email, 'content_1' => $text_editor_data1);
            $where = array( 'id' => 1, );
            $wpdb->update($table_name, $data, $where);
        }else{
            $wpdb->insert(
                $table_name,
                array( 'content' => $text_editor_data, 'email' => $email, 'content_1' => $text_editor_data1 ),
                array('%s')
            );
        }        
    }
}

add_action('init', 'my_custom_plugin_init');

function my_custom_plugin_render_page() {
    global $wpdb; 
    $site_url = get_site_url();
    $table_name = $wpdb->prefix . 'se_settings';
    $result = $wpdb->get_row("SELECT * FROM $table_name WHERE id = '1'");
    echo '
    <style>
    .booked-settings-prewrap { display: flex; justify-content: center; }
    .booked-settings-wrap { max-width: 1150px; min-width: 1000px; background: #fff; padding: 0; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 4px rgba(0, 0, 0, 0.05), 0 8px 8px rgba(0, 0, 0, 0.05), 0 32px 32px rgba(0, 0, 0, 0.05), 0 64px 64px rgba(0, 0, 0, 0.05); margin: 40px 40px 40px 20px; position: relative; }
    .booked-settings-wrap .booked-settings-title { position: relative; margin: 0; padding: 2rem 0 2.1rem 2rem; background: #2271B1; color: #fff; font-size: 1.75rem; line-height: 1rem; letter-spacing: 0; text-align: left; font-weight: 600; }
    .booked-email-settings { position: relative; padding: 40px; }
    .heading-h3 { padding: 0; margin: 0; font-size: 16px; line-height: 20px; color: #2371B1; font-weight: 600; }
    .booked-email-settings p { line-height: 1.65em; font-size: 1em; }
    .booked-email-settings input.field { display: block; margin: 0 0 20px; width: 100%; padding: 10px; font-size: 14px; line-height: 23px; }
    .mb-3{ margin-bottom: 20px; }
    .details-p{ margin-top:0px; margin-bottom:2px; padding: 0px; line-height: 0px; }
    #my_text_editor { width: 100%; min-height: 200px; }
    </style>
    <form method="post" action="" class="booked-settings-prewrap">
        <div class="wrap booked-settings-wrap">
            <div class="booked-settings-title">Sending Email</div>
            <div  class="booked-email-settings">
                <h3 class="heading-h3 mb-3" >Type Your Email.</h3>
                <input style="margin:0" name="email" value="'.$result->email.'" type="text" class="field" placeholder="example@gmail.com,example2@gmail.com">
                <p class="mb-3">If you want to write more than one email, use a comma to separate them. For example: example@gmail.com,example2@gmail.com</p>
                <p>Please Note: WordPress crons do not run unless someone visits your site. Because of this, some reminders might not get sent out. To prevent this from happening, you would need to setup cron to run from the server level using the following command:</p>
                <p><code>*/15	*	*	*	*	curl -s '.$site_url.'/manamil.php</code></p>
                <h3 class="heading-h3 mb-3" >Send Email For Confirmation to admin.</h3>
                <p>You can design your desired email template for admin using below shortcodes as well.</p>
                <p class="details-p"><b>[name]</b> — Display the full name of the customer.</p>
                <p class="details-p"><b>[email]</b> — Display the customer\'s email address.</p>
                <p class="details-p"><b>[calendar]</b> — Display the appointment\'s calendar name (if applicable).</p>
                <p class="details-p"><b>[date]</b> — Display the appointment date.</p>
                <p class="details-p"><b>[time]</b> — Display the appointment time.</p>
                <p class="details-p"><b>[customfields]</b> — Display the appointment\'s custom field data.</p>
                <p class="details-p"><b>[Conformation]</b> — Display Confirmation button yes / no.</p>
                <br><br>';
                $editor_id = 'my_text_editor2';
                $settings = array(
                    'textarea_name' => 'my_text_editor2',
                    'media_buttons' => true,
                );
                wp_editor(stripslashes($result->content_1), $editor_id, $settings);
    echo ' <br> <br>
            <h3 class="heading-h3" >Appointment Approval for Customer.</h3>
            <p>You can design your desired email template for your client using below shortcodes as well.</p>
            <p class="details-p"><b>[name]</b> — Display the full name of the customer.</p>
            <p class="details-p"><b>[email]</b> — Display the customer\'s email address.</p>
            <p class="details-p"><b>[calendar]</b> — Display the appointment\'s calendar name (if applicable).</p>
            <p class="details-p"><b>[date]</b> — Display the appointment date.</p>
            <p class="details-p"><b>[time]</b> — Display the appointment time.</p>
            <p class="details-p"><b>[customfields]</b> — Display the appointment\'s custom field data.</p>
            <br><br>';
            $editor_id = 'my_text_editor';
            $settings = array(
                'textarea_name' => 'my_text_editor',
                'media_buttons' => true,
            );
            wp_editor(stripslashes($result->content), $editor_id, $settings);
        echo'<div style="text-align:center; margin-top:30px;">
            <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
            </div>
        </div>
    </div>
</form>';
}

?>