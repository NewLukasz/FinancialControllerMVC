<?php

namespace App;

use App\Config;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 * Mail
 *
 * PHP version 7.0
 */
class Mail
{

    /**
     * Send a message
     *
     * @param string $to Recipient
     * @param string $subject Subject
     * @param string $text Text-only content of the message
     * @param string $html HTML content of the message
     *
     * @return mixed
     */
    public static function send($to, $subject, $text, $html)
    {

        $mail = new PHPMailer(true);

        try {                    
            $mail->isSMTP();                                           
            $mail->Host       = Config::HOST;                   
            $mail->SMTPAuth   = true;                        
            $mail->Username   = Config::DOMAIN_USERNAME;        
            $mail->Password   = Config::DOMAIN_PASSWORD;         
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;        
            $mail->Port       = 587;                                 

            $mail->setFrom(Config::DOMAIN_USERNAME, 'Financial Controller');
            $mail->addAddress($to);  
            $mail->isHTML(true);                                
            $mail->Subject = $subject;
            $mail->Body    = $html;
            $mail->AltBody = $text;

            $mail->send();
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
}
