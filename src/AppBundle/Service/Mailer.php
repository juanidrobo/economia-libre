<?php

namespace AppBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class Mailer {

    private $container;
    private $mailer;

    public function __construct(Container $container, \Swift_Mailer $mailerService) {
        $this->container = $container;
        $this->mailer = $mailerService;
    }

    public function sendEmail($subject, $to, $template) {
        $message = \Swift_Message::newInstance()
                ->setSubject($subject)
                ->setFrom($this->container->getParameter('mailer_user'), $this->container->getParameter('mailer_name'))
                ->setTo($to)
                ->setBody($template, 'text/html');


        $this->mailer->send($message);
    }

}
