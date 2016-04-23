<?php

namespace common;

class EmailTemplate
{
    public  $id, $from, $to, $reply, $subject, $text, $html, $headers, $attachments, $images;

    const DB = 'db';
    const TABLE = 'email_template';

    function __construct($alias, $values=NULL)
    {
        if ($alias) {
            $this->registry = registry::getInstance();
            $db = $this->registry->get('db');
            $rs = $db->query("SELECT * FROM `email_template` WHERE `alias`=".$db->escape($alias)) or die(__METHOD__ . ': '.$db->lastError);
            if ($sa = $db->fetch($rs)) {
                $this->loadDataFromArray($sa);
            }
            $this->attachments = EmailTemplateAttachment::getList($this->id);
            $this->embedded = EmailTemplateEmbedded::getList($this->id);
        }
    }

    function loadDataFromArray($array)
    {
        $this->id      = $array['id'];
        $this->from    = $array['from'];
        $this->to      = $array['to'];
        $this->reply   = $array['reply'];
        $this->subject = $array['subject'];
        $this->text    = $array['text'];
        $this->html    = $array['html'];
        $this->headers = $array['headers'];
    }

    function render($values)
    {
        if (is_array ($values)) {
            $tpl = new template();
            foreach ($values as $property => $variables) {
                $tpl->tpl = $this->$property;
                $this->$property = $tpl->apply ($variables);
            }
        }
    }

    function addImage($filespec)
    {
        $this->mime->addEmbedded(
            $filespec,
            $cid = md5(pathinfo($filespec, PATHINFO_BASENAME))
        );
        return $cid;
    }

    function send()
    {
        $registry = Registry::getInstance();
        $site_root_absolute = $registry->get('site_root_absolute');
        $mail = new PHPMailer;

#        $mail->SMTPDebug = 3;                               // Enable verbose debug output
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = 'smtp-relay.gmail.com;smtp.gmail.com';  // Specify main and backup SMTP servers
#        $mail->Host = 'smtp.gmail.com';                       // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = 'info@mmotoracks.com';              // SMTP username
        $mail->Password = 'mmotoracks321';                    // SMTP password
        $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 587;                                    // TCP port to connect to

        $mail->isHTML(true);                                  // Set email format to HTML

        $mail->setFrom($this->from /*, 'Administrator'*/);
        $mail->addAddress($this->to);     // Add a recipient
#        $mail->addAddress('ellen@example.com');               // Name is optional
        if ($this->reply) {
            $mail->addReplyTo($this->reply);
        }
#        $mail->addCC('cc@example.com');
        $mail->addBCC('mmoto@jne21.com');

        $mail->Subject = $this->subject;
        $mail->Body    = $this->html;
        $mail->AltBody = $this->text;

        if (is_array($this->attachments)) {
            foreach ($this->attachments as $attach) {
                $mail->addAttachment(
                    $site_root_absolute . self::BASE_PATH . $this->id . EmailTemplateAttahchment::PATH . $attach->filename
                );
            }
        }
        if (is_array($this->images)) {
            foreach ($this->images as $image) {
                $mail->addAttachment(
                    $site_root_absolute . self::BASE_PATH . $this->id . EmailTemplateEmbedded::PATH . $image->filename,
                    $image->cid,
                    'base64',
                    null,
                    'inline'
                );
            }
        }

        foreach ($this->makeHeaders($this->headers) as $name=>$value) {
            $mail->addCustomHeader($name, $value);
        }

        $result = $mail->send();
        if(!$result) {
            $this->errorInfo = $mail->ErrorInfo;
        }
        return $result;
    }

    function makeHeaders($s)
    {
        $strings = explode ("\n", trim($s));
        $headers = [];
        if (!empty($strings)) {
            foreach ($strings as $string) {
                $header = explode(":", $string, 2);
                $headers[trim($header[0])] = trim($header[1]);
	    }
	}
        return $headers;
    }
}
