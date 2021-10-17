<?php

/**
 * Wrapper mailer for old addons registering the "mail" method (ultra deprecated).
 */
class NewsletterMailMethodWrapper extends NewsletterMailer {

    var $mail_method;

    /**
     * The reference to the mail method.
     * 
     * @param callback $callable Must be an array with object and method to call, no other callback formats allowed.
     */
    function __construct($callable) {
        parent::__construct(strtolower(get_class($callable[0])), array());
        $this->mail_method = $callable;
    }

    function get_description() {
        if ($this->mail_method != null) {
            return 'Mail method of ' . get_class($this->mail_method[0]);
        } else {
            return 'Undetectable mailer class';
        }
    }

    function send($message) {
        if ($this->mail_method != null) {
            $r = call_user_func($this->mail_method, $message->to, $message->subject, array('html' => $message->body, 'text' => $message->body_text), $message->headers);
            if (!$r) {
                $message->error = 'Unreported error';
                return new WP_Error(self::ERROR_GENERIC, 'Unreported error');
            }
        } else {
            $message->error = 'Mail mathod not available';
            return new WP_Error(self::ERROR_FATAL, 'Mail method not available');
        }
        return true;
    }

}

/**
 * Wrapper Mailer for old addons registering the "mail" method (deprecated).
 */
class NewsletterOldMailerWrapper extends NewsletterMailer {

    var $mailer;

    /**
     * Old mailer plugin (actually untyped object)
     * @param object $mailer
     */
    function __construct($mailer) {
        $this->mailer = $mailer;
        // We have not a name, build it from the class name... and of course, no options.
        parent::__construct(strtolower(get_class($mailer)), array());
        $this->description = 'Mailer wrapper for ' . get_class($mailer);
    }

    /**
     * Only send() needs to be implemented all other method will use the defail base-class implementation
     * 
     * @param TNP_Mailer_Message $message
     * @return \WP_Error|boolean
     */
    function send($message) {
        // The old mailer manages itself the from field
        $r = $this->mailer->mail($message->to, $message->subject, array('html' => $message->body, 'text' => $message->body_text), $message->headers);
        if (!$r) {
            if (isset($this->mailer->result)) {
                $message->error = $this->mailer->result;
                return new WP_Error(self::ERROR_GENERIC, $this->mailer->result);
            } else {
                $message->error = 'Unknown error';
                return new WP_Error(self::ERROR_GENERIC, 'Unknown error');
            }
        }
        return true;
    }

}

/**
 * Standard Mailer which uses the wp_mail() function of WP.
 */
class NewsletterDefaultMailer extends NewsletterMailer {

    var $filter_active = false;

    /**
     * Static to be accessed in the hook: on some installation the object $this is not working, we're still trying to understand why
     * @var TNP_Mailer_Message 
     */
    var $current_message = null;

    function __construct() {
        parent::__construct('default', Newsletter::instance()->get_options('smtp'));
    }

    function get_description() {
        // TODO: check if overloaded
        return 'wp_mail() WordPress function (could be extended by a SMTP plugin)';
    }

    function fix_mailer($mailer) {
        $newsletter = Newsletter::instance();
        if (!empty($newsletter->options['content_transfer_encoding'])) {
            $mailer->Encoding = $newsletter->options['content_transfer_encoding'];
        } else {
            $mailer->Encoding = 'base64';
        }

        // If there is not a current message, wp_mail() was not called by us
        if (is_null($this->current_message)) {
            return;
        }

        /* @var $mailer PHPMailer */
        $mailer->Sender = $newsletter->options['return_path'];

        if (!empty($this->current_message->current_message->body) && !empty($this->current_message->current_message->body_text)) {
            $mailer->AltBody = $this->current_message->current_message->body_text;
        }
    }

    function send($message) {

        if (!$this->filter_active) {
            add_action('phpmailer_init', array($this, 'fix_mailer'), 100);
            $this->filter_active = true;
        }

        $newsletter = Newsletter::instance();
        $wp_mail_headers = array();
        // TODO: Manage the from address
        $wp_mail_headers[] = 'From: "' . $newsletter->options['sender_name'] . '" <' . $newsletter->options['sender_email'] . '>';

        if (!empty($newsletter->options['reply_to'])) {
            $wp_mail_headers[] = 'Reply-To: ' . $newsletter->options['reply_to'];
        }

        // Manage from and from name

        if (!empty($message->headers)) {
            foreach ($message->headers as $key => $value) {
                $wp_mail_headers[] = $key . ': ' . $value;
            }
        }

        if (!empty($message->body)) {
            $wp_mail_headers[] = 'Content-Type: text/html;charset=UTF-8';
            $body = $message->body;
        } else if (!empty($message->body_text)) {
            $wp_mail_headers[] = 'Content-Type: text/plain;charset=UTF-8';
            $body = $message->body_text;
        } else {
            $message->error = 'Empty body';
            return new WP_Error(self::ERROR_GENERIC, 'Message format');
        }

        $this->current_message = $message;
        $r = wp_mail($message->to, $message->subject, $body, $wp_mail_headers);
        $this->current_message = null;

        if (!$r) {
            $last_error = error_get_last();
            if (is_array($last_error)) {
                $message->error = $last_error['message'];
                if (stripos($message->error, 'Could not instantiate mail function') || stripos($message->error, 'Failed to connect to mailserver')) {
                    return new WP_Error(self::ERROR_FATAL, $last_error['message']);
                } else {
                    return new WP_Error(self::ERROR_GENERIC, $last_error['message']);
                }
            } else {
                $message->error = 'No error explanation reported';
                return new WP_Error(self::ERROR_GENERIC, 'No error message reported');
            }
        }
        return true;
    }

}

/**
 * Standard Mailer which uses the wp_mail() function of WP.
 */
class NewsletterDefaultSMTPMailer extends NewsletterMailer {

    var $mailer = null;

    function __construct($options) {
        parent::__construct('internal-smtp', $options);
    }

    function get_description() {
        return 'Internal SMTP (deprecated)';
    }

    /**
     * 
     * @param TNP_Mailer_Message $message
     * @return \WP_Error|boolean
     */
    public function send($message) {
        $logger = $this->get_logger();
        $logger->debug('Start sending to ' . $message->to);
        $mailer = $this->get_mailer();

        if (!empty($message->body)) {
            $mailer->IsHTML(true);
            $mailer->Body = $message->body;
            $mailer->AltBody = $message->body_text;
        } else {
            $mailer->IsHTML(false);
            $mailer->Body = $message->body_text;
            $mailer->AltBody = '';
        }

        $mailer->Subject = $message->subject;

        $mailer->ClearCustomHeaders();
        if (!empty($message->headers)) {
            foreach ($message->headers as $key => $value) {
                $mailer->AddCustomHeader($key . ': ' . $value);
            }
        }

        if ($message->from) {
            $logger->debug('Alternative from available');
            $mailer->setFrom($message->from, $message->from_name);
        } else {
            $newsletter = Newsletter::instance();
            $mailer->setFrom($newsletter->options['sender_email'], $newsletter->options['sender_name']);
        }

        $mailer->ClearAddresses();
        $mailer->AddAddress($message->to);
        $mailer->Send();

        if ($mailer->IsError()) {

            $logger->error($mailer->ErrorInfo);
            // If the error is due to SMTP connection, the mailer cannot be reused since it does not clean up the connection
            // on error.
            //$this->mailer = null;
            $message->error = $mailer->ErrorInfo;
            return new WP_Error(self::ERROR_GENERIC, $mailer->ErrorInfo);
        }

        $logger->debug('Sent ' . $message->to);
        //$logger->error('Time: ' . (microtime(true) - $start) . ' seconds');
        return true;
    }

    /**
     * 
     * @return PHPMailer
     */
    function get_mailer() {
        if ($this->mailer) {
            return $this->mailer;
        }

        $logger = $this->get_logger();
        $logger->debug('Setting up PHP mailer');
        require_once ABSPATH . WPINC . '/class-phpmailer.php';
        require_once ABSPATH . WPINC . '/class-smtp.php';

        $this->mailer = new PHPMailer();
        $this->mailer->IsSMTP();
        $this->mailer->Host = $this->options['host'];
        if (!empty($this->options['port'])) {
            $this->mailer->Port = (int) $this->options['port'];
        }

        if (!empty($this->options['user'])) {
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = $this->options['user'];
            $this->mailer->Password = $this->options['pass'];
        }
        $this->mailer->SMTPKeepAlive = true;
        $this->mailer->SMTPSecure = $this->options['secure'];
        $this->mailer->SMTPAutoTLS = false;

        if ($this->options['ssl_insecure'] == 1) {
            $this->mailer->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
        }

        $newsletter = Newsletter::instance();

//        if (!empty($newsletter->options['content_transfer_encoding'])) {
//            $this->mailer->Encoding = $newsletter->options['content_transfer_encoding'];
//        } else {
//            $this->mailer->Encoding = 'base64';
//        }

        $this->mailer->CharSet = 'UTF-8';
        $this->mailer->From = $newsletter->options['sender_email'];

        if (!empty($newsletter->options['return_path'])) {
            $this->mailer->Sender = $newsletter->options['return_path'];
        }
        if (!empty($newsletter->options['reply_to'])) {
            $this->mailer->AddReplyTo($newsletter->options['reply_to']);
        }

        $this->mailer->FromName = $newsletter->options['sender_name'];


        return $this->mailer;
    }

}
