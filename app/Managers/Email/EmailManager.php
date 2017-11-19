<?php

namespace App\Managers\Email;

use App\Models\Email\EmailAccount;
use Config;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;

class EmailManager
{
    /**
     * Email settings
     *
     * @var object
     */
    protected $settings;

    /**
     * Email template
     *
     * @var static
     */
    protected $email_template = 'templates.email';

    public function setEmailSettings($marketing = false)
    {
        /*Email settings*/
        $this->settings = EmailAccount::getSettings($marketing);

        /*Set mailer*/
        $this->setMailer($this->settings);
    }

    /**
     * Setup the mailer
     *
     * @param  $settings
     * @return true
     */
    protected function setMailer($settings)
    {
        $transport = \Swift_SmtpTransport::newInstance(
            $settings->host,
            $settings->port,
            $settings->encryption
        );

        /*Username*/
        if(!empty($settings->username)) {
            $transport->setUsername($settings->username);
        }

        /*Password*/
        if(!empty($settings->password)) {
            $transport->setPassword(Crypt::decrypt($settings->password));
        }

        $mailer = \Swift_Mailer::newInstance($transport);
        Mail::setSwiftMailer($mailer);

        return true;
    }

    /**
     * Send html email
     *
     * @param $data array
     * @return array|null
     */
    public function sendHtmlEmail($data)
    {
        /*Get the settings*/
        $settings = $this->settings;

        if(empty($data['to_email_address'])) {
            $errors[] = 'Receiver has no email address';
            return $errors;
        }

        Mail::send(['html' => $this->email_template],  array('content' => $data['content']), function (Message $message) use ($settings, $data) {


            $message->subject($data['subject']);

            $message->from($settings->from_email, $settings->from_name);

            if( (!empty($settings->reply_to_email)) AND (!empty($settings->reply_to_name)) ) {
                $message->replyTo($settings->reply_to_email, $settings->reply_to_name);
            }

            $message->to($data['to_email_address'], $data['to_name']);

            if(array_key_exists('pdf', $data)) {
                $message->attachData($data['pdf'], $data['filename'], array('mime' => 'application/pdf'));
            }

        });

        $errors = Mail::failures();


        if(count($errors) > 0 ) {
            return $errors;
        }else{
           //$this->fireSentEvent($settings, $data);
        }

        return null;
    }

    /**
     * Collects the data and fire the sent email event
     *
     * @param $settings object
     * @param $data array
     *
     * @return true
     */
    protected function fireSentEvent($settings, $data)
    {
        /*Build array*/
        $array = array(
            'from_name' => $settings->from_name,
            'from_email_address' => $settings->from_email,
            'reply_to_name' => $settings->reply_to_name,
            'reply_to_email_address' => $settings->reply_to_email,
            'to_name' => $data['to_name'],
            'to_email_address' => $data['to_email_address'],
            'subject' => $data['subject'],
            'content' => $data['content'],
            'filename' => (array_key_exists('filename', $data)) ? $data['filename'] : null,
            'sent_from_module' => (array_key_exists('sent_from_module', $data)) ? $data['sent_from_module'] : 'None',
        );

        /*Fire event*/
        //Event::fire(new EmailSentEvent(json_encode($array)));

        return true;
    }

}