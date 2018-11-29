<?php

/**
 * Created by PhpStorm.
 * User: sebastien
 * Date: 13/09/15
 * Time: 23:12
 */

if (!defined('ABSPATH')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit;
}

class gestsup_shortcode
{

    public function __construct()
    {
        add_shortcode('gestsup_add_ticket', array($this, 'add_ticket'));
        add_action('wp_loaded',array($this, 'thfo_add_ticket'));
    }


    public function thfo_disable_com()
    {
        global $post;
        if ($post->comment_status == "open") {
            $args = array(
                'ID' => $post->ID,
                'comment_status' => 'close',
            );
            wp_update_post($args, true);
        }
    }

    /**
     * @return string
     */
    public function add_ticket()
    {
        $this->thfo_disable_com();
        if (!null == $this->search_mail()) {
            $mail = $_POST['mail'];
        } else {
            $mail = '';
        }

        $form  = '<div class="gestsup">';
        $form .= '<form method="post" action="#" class="gestsup_form">';
        $form .= '<label class="gestsup_form_email" for="mail">' . __("Your email:", "wp-gestsup-connector") . '</label>';
        $form .= '<input class="gestsup_form_email" type="email" name="mail" value="' . $mail . '" />';
        $form .= '<label class="gestsup_form_title" for="title">' . __("Title:", "wp-gestsup-connector") . '</label>';
        $form .= '<input class="gestsup_form_title" type="text" name="title" /><label for="ticket">' . __("Ticket:", "wp-gestsup-connector") . '</label>';
        $form .= '<textarea class="gestsup_form_ticket" name="ticket" cols="50" rows="10"></textarea>
		<input class="gestsup_form_submit" type="submit" value=" ' . __('Send', 'wp-gestsup-connector') . '" name="add_ticket" >
		</form>
		</div>';
        do_action('thfo-form');
        return $form;
    }

    public function thfo_add_ticket(){
        if (isset($_POST['add_ticket']) && !empty($_POST['mail'])) {
            $search_mail = $this->search_mail();
            if ($search_mail['mail'] != $_POST['mail']) {
                _e('Your mail address isn\'t registered in our database. A mail with your request is currently sent to our support team. Thanks for having contacted us', 'wp-gestsup-connector');
                $sender = $_POST['mail'];
                $title = $_POST['title'];
                $ticket = $_POST['ticket'];
                $dest = get_option('gestsup_admin_support');
                $headers[] = 'Content-Type: text/html; charset=UTF-8';
                $headers[] = 'From:' . $sender . '<' . $sender . '>';
                $content = '<p>' . __('Dear Admin, someone is requesting help:', 'wp-gestsup-connector') . '</p>';
                //$content .= '<p>'. $ticket .'</p>';
                $content .= apply_filters('the_content', $ticket);

                wp_mail($dest, $title, $content, $headers);

                //remove_filter( 'wp_mail_content_type', 'set_html_content_type' );
                function set_html_content_type()
                {
                    return 'text/html';
                }

            } else {
                $this->add_ticket_db();
            }
        }
    }

    public function search_mail()
    {
        if (isset($_POST['add_ticket']) && !empty($_POST['mail'])) {
            $v = gestsup_options::gestsup_mysql();
            $m = $v->query("SELECT * FROM tusers WHERE mail = '$_POST[mail]'");
            $mail = $m->fetch_assoc();
            if ($mail['mail'] === $_POST['mail']) {
                return $mail;
            }
        }
    }


    public function add_ticket_db()
    {

        if (isset($_POST['add_ticket']) && $this->search_mail()) {

            $this->_ticket = apply_filters('the_content', sanitize_text_field($_POST['ticket']));
            $this->_title = sanitize_text_field($_POST['title']);
            $this->_date = current_time('Y-m-d H:m:s');
            $data_user = $this->search_mail();
            $this->_mail = $data_user['mail'];
            $user = $data_user['id'];
            $tech = get_option('gestsup_tech');

            $v = gestsup_options::gestsup_mysql();
            $v->query("INSERT INTO tincidents (technician,user,title,description,state,date_create,creator,criticality,techread) VALUES ('$tech','$user', '$this->_title','$this->_ticket','1','$this->_date','$user','4','0')");
	        $sender = $_POST['mail'];
	        $title = $_POST['title'];
	        $ticket = $_POST['ticket'];
	        $dest = get_option('gestsup_admin_support');
	        $headers[] = 'Content-Type: text/html; charset=UTF-8';
	        $headers[] = 'From:' . $sender . '<' . $sender . '>';
	        $content = '<p>' . __('Dear Admin, someone is requesting help:', 'wp-gestsup-connector') . '</p>';
	        //$content .= '<p>'. $ticket .'</p>';
	        $content .= apply_filters('the_content', $ticket);

	        wp_mail($dest, $title, $content, $headers);

	        //remove_filter( 'wp_mail_content_type', 'set_html_content_type' );
	        function set_html_content_type()
	        {
		        return 'text/html';
	        }
        }

    }


}