<?php
/**
 * Plugin Name: WS Contact Form
 * Plugin URI: https://www.silvermuru.ee/en/wordpress/plugins/ws-contact-form/
 * Description: Simple contact form for Wordpress
 * Version: 1.3.8
 * Author: UusWeb.ee
 * Author URI: https://www.wordpressi-kodulehe-tegemine.ee/
 * License: GPLv2 or later
 * Text Domain: ws-contact-form
 **/

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

class WS_Contact_Form {
    public function __construct(){
        add_action( 'plugins_loaded', array( $this, 'ws_contact_form_load_textdomain' ) );
        add_action( 'wp_footer', array( $this, 'ws_contact_form_sript' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'ws_contact_form_style' ) );
        add_action( 'wp_ajax_contacthomepage', array( $this, 'ws_contact_form_sendmail' ) );
        add_action( 'wp_ajax_nopriv_contacthomepage', array( $this, 'ws_contact_form_sendmail' ) );
        add_shortcode( 'ws-contact-form', array( $this, 'ws_contact_form_shortcode' ) );
        add_filter( 'widget_text', 'do_shortcode' );
    }

    public function ws_contact_form_load_textdomain() {
        load_plugin_textdomain( 'ws-contact-form', false, plugin_basename( dirname( __FILE__ ) ) . '/languages/' );
    }

    public function ws_contact_form_shortcode( $atts, $content = null ){
        ob_start();
        if (get_option('wscontact-form-title-option') != false) {
            echo '<h3> ' . esc_html(get_option('wscontact-form-title-option')) . '</h3>';
        }

        $random_form_id = wp_rand(1, 1000);
        ?>
        <form id="ws_contact_form" class="ws-form-group" data-id="<?php echo esc_html($random_form_id); ?>">
			<?php wp_nonce_field( 'contact_form_send_control', 'contact_form_send_control_field' ); ?>
            <div class="ws-hidden ws-message-response ws-message-successful" id="ws_contact_success"><?php esc_html_e( 'Message is sent successfully!', 'ws-contact-form' ); ?></div>
            <div class="ws-hidden ws-message-response ws-message-error" id="ws_contact_error"><?php esc_html_e( 'There is problem with form', 'ws-contact-form' ); ?></div>
            <input class="ws-form-control" type="text" name="ws_contact_name" id="ws_contact_name" placeholder="<?php esc_html_e( 'Name', 'ws-contact-form' ); ?> *">
            <input class="ws-form-control" type="text" name="ws_contact_email" id="ws_contact_email" placeholder="<?php esc_html_e( 'Email', 'ws-contact-form' ); ?> *">
            <textarea class="ws-form-control" name="ws_contact_comment" id="ws_contact_comment" placeholder="<?php esc_html_e( 'Content', 'ws-contact-form' ); ?> *"></textarea>
            <button type="submit" id="ws_send_form"><?php esc_html_e( 'Send', 'ws-contact-form' ); ?></button>
        </form>
        <?php
        return ob_get_clean();
    }

    public function ws_contact_form_sript() {
        wp_register_script( 'ws-contact-form-script', plugin_dir_url(__FILE__) . 'js/scripts.js', array( 'jquery' ), true, true );
        wp_enqueue_script( 'ws-contact-form-script' );
    }

    public function ws_contact_form_style() {
		global $post;
        if (has_shortcode($post->post_content, 'ws-contact-form')) {
			wp_register_style('ws-contact-form-style', plugin_dir_url(__FILE__) . 'css/style.css', true, true);
			wp_enqueue_style('ws-contact-form-style');
		}
    }

    public function ws_contact_form_sendmail()
    {
        $ws_cf_sender_name = esc_attr(get_option('wscontact-form-sender-name-option'));
        $ws_cf_sender_email = esc_attr(get_option('wscontact-form-sender-email-option'));

		if ( ! isset( $_POST['contact_form_send_control_field'] ) || ! wp_verify_nonce( $_POST['contact_form_send_control_field'], 'contact_form_send_control' )) {
			$email = $_POST['ws_contact_email'];
			$headers = "From: " . $ws_cf_sender_name . " <" . get_option('wscontact-form-sender-email-option') . "> \r\n Reply-To: " . $email;
			$message = $_POST['ws_contact_comment'];
			$name = $_POST['ws_contact_name'];

			function ws_email_valid($email)
			{
				$email_pattern = "/^[a-z\d](\.?[a-z\d_\-]+)*@([a-z\d](\.?[a-z\d\-]+)*\.[a-z]{2,4})$/i";
				$result = preg_match($email_pattern, $email, $match);
				if (FALSE !== $result && $result > 0) {
					return TRUE;
				}
				return FALSE;
			}

			if (isset($name) && ws_email_valid($email) && isset($message)) {
				$ws_cf_recipient_name = esc_attr(get_option('wscontact-form-recipient-name-option'));
				$ws_cf_recipient_email = esc_attr(get_option('wscontact-form-recipient-email-option'));
				$ws_cf_email_title = esc_attr(get_option('wscontact-form-email-title-option'));
				wp_mail($ws_cf_recipient_name . " <" . $ws_cf_recipient_email . ">", $ws_cf_email_title, $ws_cf_email_title . ":\r\n " . __('Name', 'ws-contact-form') . ": " . $name . " \r\n " . __('Email', 'ws-contact-form') . ": " . $email . " \r\n " . __('Content', 'ws-contact-form') . ": " . $message, $headers);
				echo 'OK';
			} else {
				echo 'ERROR';
			}
		}
		else {
			echo 'ERROR';
		}

        die();
    }


}

if ( is_admin() ) {
    require plugin_dir_path( __FILE__ ) . '/admin/ws-contact-form-admin.php';
    $wpse_ws_contact_form_plugin_admin = new WS_Contact_Form_admin();
}

$wpse_ws_contact_form_plugin = new WS_Contact_Form();
?>
