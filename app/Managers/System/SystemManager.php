<?php

namespace App\Managers\System;

use App\Managers\Email\EmailManager;
use App\Models\Error\ErrorException;
use App\Models\System\SystemEmail;
use Illuminate\Support\Facades\App;
use Config;

class SystemManager
{
    const ERROR_EXCEPTION_EMAIL_SUBJECT = 'Error exception';

    /**
     * Construct - set all arrays and objects
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Returns the html template
     *
     * @param $ErrorException ErrorException
     * @return bool
     */
    public function sendExceptionEmails($ErrorException)
    {
        /*Get all the system email entries*/
        $SystemEmails = SystemEmail::where('receive_emails', 1)->get();

        if(count($SystemEmails) > 0) {

            $email = $this->createErrorExceptionEmail($ErrorException);

            /*Get the email manager*/
            $emailManager = App::make(EmailManager::class);

            /*Set email settings*/
            $emailManager->setEmailSettings(false);

            foreach ($SystemEmails as $systemEmail) {

                $array = array(
                    'subject' =>  $email['subject'],
                    'content' => $email['content'],
                    'to_name' => $systemEmail->name,
                    'to_email_address' => $systemEmail->email_address,
                );

                /*Send email*/
                $emailManager->sendHtmlEmail($array);
            }

            return true;
        }

        return false;
    }

    /**
     * Creates the email content for the error exception
     *
     * @param $ErrorException ErrorException
     * @return array
     */
    protected function createErrorExceptionEmail($ErrorException)
    {
        /*Set array*/
        $array = array(
            'subject' => self::ERROR_EXCEPTION_EMAIL_SUBJECT,
            'content' => $this->getErrorExceptionEmailContent($ErrorException)
        );

        return $array;
    }

    /**
     * Creates the content for the error exception email
     *
     * @param $ErrorException ErrorException
     * @return string
     */
    protected function getErrorExceptionEmailContent($ErrorException)
    {
        $content = '';

        $content.= "<p><b>Message:</b> $ErrorException->message</p>";
        $content.= "<p><b>Code:</b> $ErrorException->code</p>";
        $content.= "<p><b>File:</b> $ErrorException->file</p>";
        $content.= "<p><b>Line:</b> $ErrorException->line</p>";
        $content.= "<p><b>Uri:</b> $ErrorException->uri</p>";
        $content.= "<p><b>Date/Time: </b>".date_format(date_create($ErrorException->created_at), 'd-M-Y H:i:s')."</p>";
        $content.= "<p><b>Trace:</b></p>";
        $content.= $ErrorException->getFormattedTrace();

        return $content;
    }

}