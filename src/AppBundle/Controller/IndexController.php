<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\User;
use AppBundle\Entity\Question;

class IndexController extends Controller {

    public function testAction(Request $request) {

        return $this->render('AppBundle:index:test.html.twig');
   
     
    }

    public function indexAction(Request $request) {
        $session = $request->getSession();
        $userSession = $session->get("userSession");
        return $this->render('AppBundle:index:index.html.twig', array("userSession" => $userSession));
    }

    public function aboutAction(Request $request) {
        $session = $request->getSession();
        $userSession = $session->get("userSession");
        return $this->render('AppBundle:index:about.html.twig', array("userSession" => $userSession));
    }

    public function newQuestionAction(Request $request) {

        $session = $request->getSession();
        $userSession = $session->get("userSession");
        $responseArray = Array();

        $response = new Response();
        $questionText = $request->request->get('question');
        if ($questionText === null) {
            $response->setStatusCode(400);
            $response->setContent('{"error": "null post data "}');
            return $response;
        }
        if ($questionText === "") {
            $response->setStatusCode(400);
            $response->setContent('{"error": "Escribe una pregunta!"}');
            return $response;
        }

        $captcha = $request->request->get('captcha');
        if ($captcha === null) {
            $response->setStatusCode(400);
            $response->setContent('{"error": "null post captcha "}');
            return $response;
        }

        $reCaptcha = $this->get('app.captcha');
        $captchaData = $reCaptcha->validateCaptcha($captcha);
        $captchaJson = json_decode($captchaData, true);
        if ($captchaJson['success'] === false) {
            $response->setStatusCode(300);
            //  $response->setContent('{"error": "Error en el captcha."}');
            $response->setContent(json_encode($captchaJson));
            return $response;
        }



        $em = $this->getDoctrine()->getManager();
        $user = null;
        if ($userSession) {
            $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($userSession->getCode());
        }
        $question = new Question();
        $question->setQuestion($questionText);
        $question->setUser($user);
        $em->persist($question);
        $em->flush();
        $responseArray['success'] = "Pregunta enviada, muchas gracias!";


        $response->setContent(json_encode($responseArray));
        return $response;
    }
    
        public function helpAction(Request $request) {
        $session = $request->getSession();
        $userSession = $session->get("userSession");
        return $this->render('AppBundle:index:help.html.twig', array("userSession" => $userSession));
    }

}
