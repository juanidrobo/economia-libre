<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use AppBundle\Entity\Promise;
use AppBundle\Entity\User;
use AppBundle\Entity\UserInfo;
use AppBundle\Entity\Event;
use AppBundle\Entity\Review;

class PromiseController extends Controller {

    public function newAction(Request $request) {

//pick random promise from one user to show as an example
        $session = $request->getSession();
        $userSession = $session->get("userSession");
        $renderArray = Array();
        $renderArray['userSession'] = $userSession;
        $renderArray['csrf'] = uniqid("", true);
        $promises = $this->getDoctrine()->getRepository('AppBundle:Promise')->findBy(array('responsible' => 'juanidrobo@gmail.com'));

        if ($promises) {
            if (!empty($promises)) {
                $rnd = rand(0, count($promises) - 1);
                $renderArray['promise'] = $promises[$rnd];
            }
        } else {
            $renderArray['promise'] = null;
        }

        return $this->render('AppBundle:promise:new.html.twig', $renderArray);
    }

    public function createAction(Request $request) {

        $session = $request->getSession();
        $userSession = $session->get("userSession");
        $responseArray = Array();


        if ($userSession) {
            $response = new Response();
            $promiseJson = $request->request->get('promise');
            if ($promiseJson === null) {
                $response->setStatusCode(400);
                $response->setContent('{"error": "null post data "}');
                return $response;
            }

            $captcha = $request->request->get('captcha');
            if ($captcha === null) {
                $response->setStatusCode(400);
                $response->setContent('{"error": "null post captcha "}');
                return $response;
            }

            $reCaptcha = $this->get('app.captcha');
            $captchaJson = $reCaptcha->validateCaptcha($captcha);
            $captchaArray = json_decode($captchaJson, true);
            if ($captchaArray['success'] === false) {
                $response->setStatusCode(300);
                $response->setContent('{"error": "Error en el captcha."}');
                $response->setContent(json_encode($captchaArray));
                return $response;
            }

            $promiseArray = json_decode($promiseJson, true);



            if ($promiseArray["description"] === "") {
                $response->setStatusCode(300);
                $response->setContent('{"error": "La descripción no puede ser vacia."}');
                return $response;
            }



            $em = $this->getDoctrine()->getManager();

            $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($userSession->getCode());

            $promise = new Promise();
            $promise->setDescription($promiseArray["description"]);

            $promise->setActive(true);
            $promise->setVisible(true);
            $promise->setResponsible($user);
            $em->persist($promise);
            $em->flush();
            $responseArray['promise'] = $promise->getCode();

            $event = new Event();
            $event->setPromise($promise);
            $event->setAction("new");
            $event->setOwner($user);
            $event->setReceiver($user);
            $em->persist($event);
            $em->flush();

            /*  $mailer = $this->get('app.mailer');
              $template = $this->renderView("emails/activate-new-promise.html.twig", array('object' => $promise->getCode()));
              $mailer->sendEmail("Nueva moneda creada", $promise->getResponsible()->getEmail(), $template);
              $response->setContent('{"success":"Moneda creada"}');
             */
            $session->getFlashBag()->add('created', 'Esta moneda ha sido creada, responde por lo que prometes!');
            $responseArray['success'] = "Moneda creada";
        } else {
            $response->setStatusCode(300);
            $responseArray['error'] = "No ha inicido sesión.";
        }

        $response->setContent(json_encode($responseArray));
        return $response;
    }

    //Activate promise
    public function activateAction(Request $request, $promiseCode, $eventCode) {
        $session = $request->getSession();

        $renderArray = Array();
        $renderArray['csrf'] = uniqid("", true);
        $renderArray['userSession'] = null;

        $user = null;
        $event = null;

        $em = $this->getDoctrine()->getManager();

        $promise = $this->getDoctrine()->getRepository('AppBundle:Promise')->find($promiseCode);
        if ($promise === null) {
            return $this->render('AppBundle:promise:activate-error.html.twig', $renderArray);
        }
        $renderArray['promise'] = $promise;
        $event = $this->getDoctrine()->getRepository('AppBundle:Event')->findOneBy(array("promise" => $promise->getCode()), array("date" => "DESC"));


        $renderArray['event'] = $event;
        // if the code matches, and only if the last event is pending or transfer
        if ($event->getCode() === $eventCode && ($event->getAction() === "pending" || $event->getAction() === "transfer")) {

            $userCode = $event->getReceiver()->getCode(); // this has to be the own who was transfered it

            $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($userCode);
            if ($user) {

                $renderArray['user'] = $user;
                if ($user->getActive()) {
                    $session->set("userSession", $user);
                    $renderArray['userSession'] = $user;
                    $newEvent = new Event();
                    $newEvent->setPromise($promise);
                    $newEvent->setAction("grab");
                    $newEvent->setReceiver($user);

                    $em->persist($newEvent);
                    $em->flush();
                    return $this->render('AppBundle:promise:activated.html.twig', $renderArray);
                } else {
                    $session->set("userSession",null);
                    return $this->render('AppBundle:promise:activate-missing-key.html.twig', $renderArray);
                }
            } else { //TO REMOVE?? 
                $user = $this->registerUser($userEmail);
                return $this->render('AppBundle:promise:activate-missing-key.html.twig', $renderArray);
            }

            return $this->render('AppBundle:promise:activate-missing-key.html.twig', $renderArray);
        } else {

            $renderArray['code'] = $promise->getCode();

            return $this->redirectToRoute('promise', $renderArray);
        }
    }

    public function promiseAction(Request $request, $code) {

        $session = $request->getSession();
        $userSession = $session->get("userSession");
        $message = $session->getFlashBag()->get('created');

        $renderArray = Array();
        $renderArray['userSession'] = $userSession;
        $renderArray['csrf'] = uniqid("", true);
        $renderArray['message'] = $message;

        $review = null;
        if (strpos($code, '&')) {
            $keywords = preg_split("/[&]/", $code);
            $promiseCode = $keywords[0];
            $eventCode = $keywords[1];
            $promise = $this->getDoctrine()->getRepository('AppBundle:Promise')->findOneBy(array('code' => $promiseCode, 'active' => true));
            if ($promise === null) {
                return $this->render('AppBundle:promise:promise-error.html.twig', $renderArray);
            }
            $event = $this->getDoctrine()->getRepository('AppBundle:Event')->findOneBy(array("promise" => $promise->getCode()), array("date" => "DESC"));
            if ($event) {
                if ($event->getAction() === "anonymousgrab") {
                    if ($event->getCode() !== $eventCode) {
                        $renderArray['promise'] = $promise;
                        return $this->render('AppBundle:promise:promise-error-anonymous.html.twig', $renderArray);
                    }
                } else {
                    return $this->render('AppBundle:promise:promise-error.html.twig', $renderArray);
                }
            } else {
                return $this->render('AppBundle:promise:promise-error.html.twig', $renderArray);
            }
        } else {

            $promise = $this->getDoctrine()->getRepository('AppBundle:Promise')->findOneBy(array('code' => $code, 'active' => true));
            if ($promise === null) {
                return $this->render('AppBundle:promise:promise-error.html.twig', $renderArray);
            }
            $event = $this->getDoctrine()->getRepository('AppBundle:Event')->findOneBy(array("promise" => $promise->getCode()), array("date" => "DESC"));
            if ($event) {
                if ($event->getAction() === "anonymousgrab") {
                    $renderArray['promise'] = $promise;
                    return $this->render('AppBundle:promise:promise-error-anonymous.html.twig', $renderArray);
                }
                if ($event->getAction() === "review") {
                    $review = $this->getDoctrine()->getRepository('AppBundle:Review')->findOneBy(array('promise' => $promise->getCode()));
                }
            }
        }
        $renderArray['promise'] = $promise;
        $renderArray['event'] = $event;
        $renderArray['review'] = $review;

        return $this->render('AppBundle:promise:promise.html.twig', $renderArray);
    }

    public function qrCodeAction($code) {
        $response = new Response();
        include_once $this->get('kernel')->getRootDir() . '/../src/AppBundle/Lib/phpqrcode/qrlib.php';

        $url = $this->generateUrl('promise', array('code' => $code), UrlGeneratorInterface::ABSOLUTE_URL);
        $response->setContent(\QRcode::png($url));
        return $response;
    }

    public function releaseAction(Request $request) {
        $session = $request->getSession();
        $userSession = $session->get("userSession");

        if ($userSession) {

            $response = new Response();
            $promiseJson = $request->request->get('promise');
            if ($promiseJson === null) {
                $response->setStatusCode(400);
                $response->setContent('{"error": "post data es nulo"}');
                return $response;
            }
            $promiseArray = json_decode($promiseJson, true);
            $promiseCode = $promiseArray['promise'];

            $em = $this->getDoctrine()->getManager();
            $promise = $this->getDoctrine()->getRepository('AppBundle:Promise')->findOneBy(array("code" => $promiseCode));
            if (empty($promise)) {
                $response->setStatusCode(300);
                $response->setContent('{"error": "Error con la moneda."}');
                return $response;
            }
// get the last event of the promise
            $event = $this->getDoctrine()->getRepository('AppBundle:Event')->findOneBy(array("promise" => $promiseCode), array("date" => "DESC"));
            if (empty($event)) { //this onlu happens with old data, new money has the "new" event 
                $response->setStatusCode(300);
                $response->setContent('{"error": "La moneda no tiene eventos."}');
                return $response;
            } else { //if there are events!
                //only "new", "grab" and "publickey" events 
                if ($event->getAction() === "new") {
                    if ($userSession->getCode() === $promise->getResponsible()->getCode()) {
                        $event = new Event();
                        $event->setPromise($promise);
                        $event->setAction("release");
                        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($userSession->getCode());

                        $event->setOwner($user);
                        $event->setReceiver($user);
                        $em->persist($event);
                        $em->flush();
                    } else {
                        $response->setStatusCode(300);
                        $response->setContent('{"error": "No eres el propietario actual."}');
                        return $response;
                    }
                } elseif ($event->getAction() === "grab" || $event->getAction() === "publickey") {
                    if ($event->getReceiver()->getCode() === $userSession->getCode()) {
                        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($userSession->getCode());

                        $event = new Event();
                        $event->setPromise($promise);
                        $event->setAction("release");
                        $event->setOwner($user);
                        $event->setReceiver($user);
                        $em->persist($event);
                        $em->flush();
                    } else {
                        $response->setStatusCode(300);
                        $response->setContent('{"error": "No eres el propietario actual."}');
                        return $response;
                    }
                } else {

                    //other events
                    $response->setStatusCode(300);
                    $response->setContent('{"error": "No se puede liberar esta moneda."}');
                    return $response;
                }
            }
        } else {
            $response->setStatusCode(300);
            $response->setContent('{"error": "No session."}');
            return $response;
        }
        $response->setContent('{"success":"Moneda liberada!"}');
        return $response;
    }

    public function grabAction(Request $request) {
        $session = $request->getSession();
        $userSession = $session->get("userSession");

        if ($userSession) {

            $response = new Response();

            $promiseJSON = $request->request->get('promise');
            if ($promiseJSON === null) {
                $response->setStatusCode(400);
                $response->setContent('{"error": "Petición inválida"}');
                return $response;
            }
            $promiseArray = json_decode($promiseJSON, true);

            $promiseCode = $promiseArray['promise'];
            $publicKey = $promiseArray['pubkey'];

            $event = $this->getDoctrine()->getRepository("AppBundle:Event")->findOneBy(array("promise" => $promiseCode), array("date" => "DESC"));
            $promise = $this->getDoctrine()->getRepository('AppBundle:Promise')->findOneBy(array("code" => $promiseCode));
            $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($userSession->getCode());


            if ($event->getAction() === "release" || $event->getAction() === "anonymousgrab") {

                $em = $this->getDoctrine()->getManager();
                $event = new Event();
                $event->setPromise($promise);
                $event->setAction("grab");
                $event->setReceiver($user);
                $em->persist($event);
                $em->flush();
            }

            if ($event->getAction() === "publickey") {
                if ($promise->getPubKey() === $publicKey) {
                    $em = $this->getDoctrine()->getManager();
                    $event = new Event();
                    $event->setPromise($promise);
                    $event->setAction("grab");
                    $event->setReceiver($user);
                    $em->persist($event);
                    $em->flush();
                } else {
                    $response->setStatusCode(300);
                    $response->setContent('{"error": "La llave publica es incorrecta."}');
                    return $response;
                }
            }

            if ($user->getName()) {
                $userNameToDisplay = $user->getName();
            } elseif ($user->getEmail()) {
                $userNameToDisplay = $user->getEmail();
            } elseif ($user->getPhone()) {
                $userNameToDisplay = $user->getPhone();
            }
            $response->setContent('{"success":"La moneda ahora pertenece a ' . $userNameToDisplay . '"}');
            return $response;
        } else {
            $response->setStatusCode(300);
            $response->setContent('{"error": "No session."}');
            return $response;
        }

        return $response;
    }

    public function newPublicKeyAction(Request $request) {

        $session = $request->getSession();
        $userSession = $session->get("userSession");

        if ($userSession) {

            $response = new Response();

            $promiseJSON = $request->request->get('promise');

            if ($promiseJSON === null) {

                $response->setStatusCode(400);
                $response->setContent('{"error": "post data es nulo "}');
                return $response;
            }
            $promiseArray = json_decode($promiseJSON, true);

            $promiseCode = $promiseArray['promise'];
            $publicKey = $promiseArray ['publickey'];

            $em = $this->getDoctrine()->getManager();
            $promise = $this->getDoctrine()->getRepository('AppBundle:Promise')->findOneBy(array("code" => $promiseCode));

            if (empty($promise)) {
                $response->setStatusCode(300);
                $response->setContent('{"error": "Error con la moneda."}');
                return $response;
            }


            $event = $this->getDoctrine()->getRepository('AppBundle:Event')->findOneBy(array("promise" => $promise), array("date" => "DESC"));

            if ($event->getAction() === "new" || $event->getAction() === "grab" || $event->getAction() == "publickey") {

                if ($event->getReceiver()->getCode() === $userSession->getCode()) {
                    $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($userSession);

                    $event = new Event();
                    $event->setPromise($promise);
                    $event->setAction("publickey");
                    $event->setOwner($user);
                    $event->setReceiver($user);
                    $promise->setPubKey($publicKey);
                    $em->persist($event);
                    $em->persist($promise);
                    $em->flush();
                } else {
                    $response->setStatusCode(300);
                    $response->setContent('{"error": "No eres el propietario actual."}');
                    return $response;
                }
            } else {

                $response->setStatusCode(300);
                $response->setContent('{"error": "No se puede proteger esta moneda con llave publica."}');
                return $response;
            }
        } else {
            $response->setStatusCode(300);
            $response->setContent('{"error": "No session."}');
            return $response;
        }

        $response->setContent('{"success":"Moneda protegida con llave publica!"}');
        return $response;
    }

    public function transferAction(Request $request) {
        $session = $request->getSession();
        $userSession = $session->get("userSession");

        if ($userSession) {

            $response = new Response();

            $promiseJSON = $request->request->get('promise');
            if ($promiseJSON === null) {
                $response->setStatusCode(400);
                $response->setContent('{"error": "post data es nulo "}');
                return $response;
            }
            $promiseArray = json_decode($promiseJSON, true);

            $promiseCode = $promiseArray['promise'];
            $emailToTransfer = $promiseArray['transfer'];

            if (!filter_var($emailToTransfer, FILTER_VALIDATE_EMAIL)) {
                $response->setStatusCode(300);
                $response->setContent('{"error": "El correo para transferir tiene que ser valido." }');
                return $response;
            }

            $em = $this->getDoctrine()->getManager();
            $promise = $this->getDoctrine()->getRepository('AppBundle:Promise')->findOneBy(array("code" => $promiseCode));
            if (empty($promise)) {
                $response->setStatusCode(300);
                $response->setContent('{"error": "Error con la moneda."}');
                return $response;
            }
            $event = $this->getDoctrine()->getRepository('AppBundle:Event')->findOneBy(array("promise" => $promiseCode), array("date" => "DESC"));

            if (empty($event)) {
                $response->setStatusCode(300);
                $response->setContent('{"error": "La moneda no tiene eventos."}');

                return $response;
            }

            $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($userSession->getCode());


            //check if the user you are gonna transfer exists, if not, register, active false
            $userInfo = $this->getDoctrine()->getRepository('AppBundle:UserInfo')->findOneBy(array("type" => "email", "info" => $emailToTransfer));
            if (!$userInfo) {
                $userToTransfer = $this->registerUser($emailToTransfer);
            } else {
                $userToTransfer = $userInfo->getUser();
            }

            if ($event->getAction() === "new") { // it has to be the same responsible
                if ($promise->getResponsible()->getCode() === $userSession->getCode()) {
                    $event = new Event();
                    $event->setPromise($promise);
                    $event->setAction("transfer");
                    $event->setOwner($user);
                    $event->setReceiver($userToTransfer);
                    $em->persist($event);
                    $em->flush();
                    $mailer = $this->get('app.mailer');
                    $template = $this->renderView('emails/activate-transfer-promise.html.twig', array('object' => $event));
                    $mailer->sendEmail("Te han transferido una moneda", $emailToTransfer, $template);
                    $response->setContent('{"success":"Un correo ha sido enviado a ' . $emailToTransfer . ' con un link para confirmar la transacción. De no confirmarse en 7 días se cancelará."}');
                } else {
                    $response->setStatusCode(300);
                    $response->setContent('{"error": "El correo del responsable no coincide con la sesion."}');
                    return $response;
                }
            } else {


// if the current owner is the one the user claims
                if ($event->getReceiver()->getCode() === $userSession->getCode()) {
//ONLY after a grab action or publickey action
                    if ($event->getAction() === "grab" || $event->getAction() === "publickey") {

                        $event = new Event();
                        $event->setPromise($promise);
                        $event->setAction("transfer");
                        $event->setOwner($user);
                        $event->setReceiver($userToTransfer);
                        $em->persist($event);
                        $em->flush();

                        $mailer = $this->get('app.mailer');
                        $template = $this->renderView("emails/activate-transfer-promise.html.twig", array('object' => $event));
                        $mailer->sendEmail("Te han transferido una moneda", $emailToTransfer, $template);
                        $response->setContent('{"success":"Un correo ha sido enviado a ' . $emailToTransfer . ' con un link para confirmar la transacción. De no confirmarse en 7 días se cancelará."}');
                    } else {

                        $response->setStatusCode(300);
                        $response->setContent('{"error": "No se puede transferir esta moneda."}');
                        return $response;
                    }
                } else {
                    $response->setStatusCode(300);
                    $response->setContent('{"error": "El actual dueño de la moneda no corresponde al de la sesion."}');
                    return $response;
                }
            }
        } else {
            $response->setStatusCode(300);
            $response->setContent('{"error": "No session."}');
            return $response;
        }
        return $response;
    }

    public function claimAction(Request $request) {
        $session = $request->getSession();
        $userSession = $session->get("userSession");

        if ($userSession) {
            $response = new Response();

            $promiseJSON = $request->request->get('promise');
            if ($promiseJSON === null) {
                $response->setStatusCode(400);
                $response->setContent('{"error": "post data es nulo"}');
                return $response;
            }
            $promiseArray = json_decode($promiseJSON, true);

            $promiseCode = $promiseArray['code'];

            $em = $this->getDoctrine()->getManager(
            );

            $promise = $this->getDoctrine()->getRepository('AppBundle:Promise')->findOneBy(array("code" => $promiseCode));

            if (empty($promise)) {
                $response->setStatusCode(300);
                $response->setContent('{"error": "Error con la moneda."}');
                return $response;
            }

// get the last event of the promise
            $event = $this->getDoctrine()->getRepository('AppBundle:Event')->findOneBy(array("promise" => $promise), array("date" => "DESC"));

            if (empty($event)) {
                $response->setStatusCode(300);
                $response->setContent('{"error": "La moneda no puede ser reclamada."}');
                return $response;
            } else {

                if ($event->getAction() === "grab" || $event->getAction() === "publickey") {
                    if ($event->getReceiver()->getCode() === $userSession->getCode()) {

                        // it cannot be review by the responsible of the promise
                        if ($promise->getResponsible()->getCode() === $userSession->getCode()) {
                            $response->setStatusCode(300);
                            $response->setContent('{"error": "No puedes reclamar tu propia moneda."}');
                            return $response;
                        }
                        $owner = $this->getDoctrine()->getRepository('AppBundle:User')->find($userSession);

                        $event = new Event();
                        $event->setPromise($promise);
                        $event->setAction("claim");
                        $event->setOwner($owner);
                        $event->setReceiver($promise->getResponsible());
                        $promise->setVisible(true);
                        $em->persist($event);
                        $em->persist($promise);
                        $em->flush();
                        if ($promise->getResponsible()->getEmail() != null) {
                            $mailer = $this->get('app.mailer');
                            $template = $this->renderView("emails/claim-promise.html.twig", array('object' => $event));
                            $mailer->sendEmail("Han reclamado una moneda creada por ti", $promise->getResponsible()->getEmail(), $template);
                        } else {
                            $response->setContent('{"success":"El creador o responsable ' . $promise->getResponsible()->getDisplayName() . ' no tiene correo configurado en su cuenta, verifica la información publica para darle aviso de la voluntad de usar la moneda."}');
                            return $response;
                        }
                    } else {
                        $response->setStatusCode(300);
                        $response->setContent('{"error": "No eres el propietario actual."}');
                        return $response;
                    }
                } else {

                    $response->setStatusCode(300);
                    $response->setContent('{"error": "No se puede reclamar esta moneda. "}');
                    return $response;
                }
            }

            $response->setContent('{"success":"Una notificación ha sido enviada a ' . $promise->getResponsible()->getEmail() . '. También puedes notificarle por medio de la información de contacto en la pagina de perfil público."}');
        } else {
            $response->setStatusCode(300);
            $response->setContent('{"error": "No session."}');
            return $response;
        }
        return $response;
    }

    public function grabAnonymousAction(Request $request) {
        $response = new Response();

        $userData = $request->request->get('user');
        if ($userData === null) {
            $response->setStatusCode(400);
            $response->setContent('{"error": "post data es nulo"}');
            return $response;
        }
        $userJson = json_decode($userData, true);
        $promiseCode = $userJson['promise'];

        $em = $this->getDoctrine()->
                getManager();

        $promise = $this->getDoctrine()->getRepository('AppBundle:Promise')->findOneBy(array("code" => $promiseCode));

        if (empty($promise)) {
            $response->setStatusCode(300);
            $response->setContent('{"error": "Error con la moneda."}');
            return $response;
        }

// get the last event of the promise
        $event = $this->getDoctrine()->getRepository('AppBundle:Event')->findOneBy(array("promise" => $promiseCode), array("date" => "DESC"));
        if (empty($event)) {
            $response->setStatusCode(300);
            $response->setContent('{"error": "La moneda no puede ser reclamada de forma anónima."}');
            return $response;
        } else {
            if ($event->getAction() === "release" || $event->getAction() === "anonymousgrab") {
                $event = new Event();
                $event->setPromise($promise);
                $event->setAction("anonymousgrab");

                $em->persist($event);
                $em->flush();
            } else {

                $response->setStatusCode(300);
                $response->setContent('{"error": "No se puede reclamar esta moneda de forma anónima. "}');
                return $response;
            }
        }

        $response->setContent('{"success":"La moneda fue recogida de manera anónima. En breve se mostrará la nueva dirección.","code":"' . $event->getCode() . '"}');
        return $response;
    }

    public function reviewAction(Request $request) {
        $session = $request->getSession();
        $userSession = $session->get("userSession");
        if ($userSession) {
            $response = new Response();
            $reviewJSON = $request->request->get('review');
            if ($reviewJSON === null) {
                $response->setStatusCode(400);
                $response->setContent('{"error": "post data es nulo"}');
                return $response;
            }
            $reviewArray = json_decode($reviewJSON, true);
            $promiseCode = $reviewArray['promise'];
            $reviewText = $reviewArray['review'];


            if ($reviewText === null || $reviewText === "") {
                $response->setStatusCode(300);
                $response->setContent('{"error": "Escribe algún comentario."}');
                return $response;
            }

            $em = $this->getDoctrine()->getManager();

            $promise = $this->getDoctrine()->getRepository('AppBundle:Promise')->findOneBy(array("code" => $promiseCode));

            if (empty($promise)) {
                $response->setStatusCode(300);
                $response->setContent('{"error": "Error con la moneda."}');
                return $response;
            }

// get the last event of the promise
            $event = $this->getDoctrine()->getRepository('AppBundle:Event')->findOneBy(array("promise" => $promiseCode), array("date" => "DESC"));

            if (empty($event)) {
                $response->setStatusCode(300);
                $response->setContent('{"error": "La moneda no puede ser valorada."}');
                return $response;
            } else { //if there events!
                //ONLY can release after a grab action
                if ($event->getAction() === "claim") {

                    if ($userSession->getCode() === $event->getOwner()->getCode()) {

                        $owner = $this->getDoctrine()->getRepository('AppBundle:User')->find($userSession);
                        $receiver = $this->getDoctrine()->getRepository('AppBundle:User')->find($promise->getResponsible());

                        $review = new Review();
                        $review->setPromise($promise);
                        $review->setUser($owner);
                        $review->setReview($reviewText);
                        $em->persist($review);

                        $event = new Event();
                        $event->setPromise($promise);
                        $event->setOwner($owner);
                        $event->setReceiver($receiver);
                        $event->setAction("review");
                        $em->persist($event);
                        $em->flush();

                        $mailer = $this->get('app.mailer');
                        $template = $this->renderView('emails/review-promise.html.twig', array('object' => $event));
                        $mailer->sendEmail("Han valorado una moneda creada por ti", $promise->getResponsible()->getEmail(), $template);
                    } else {
                        $response->setStatusCode(300);
                        $response->setContent('{"error": "No fuiste tú el que reclamó la moneda."}');
                        return $response;
                    }
                } else {

                    $response->setStatusCode(300);
                    $response->setContent('{"error": "No se puede reclamar esta moneda. "}');
                    return $response;
                }
            }
        } else {
            $response->setStatusCode(300);
            $response->setContent('{"error": "No session."}');
            return $response;
        }

        $response->setContent('{"success":"Muchas gracias por tus palabras, estas servirán para tomar decisiones en el futuro."}');
        return $response;
    }

    private function registerUser($email) {
        $em = $this->getDoctrine()->getManager();
        $user = new User();
        $user->setEmail($email);
        $user->setActive(false);
        $em->persist($user);
        $em->flush();
        $userInfo = new UserInfo();
        $userInfo->setUser($user);
        $userInfo->setType("email");
        $userInfo->setInfo($user->getEmail());
        $userInfo->setActive(false);
        $userInfo->setVisible(true);
        $em->persist($userInfo);
        $em->flush();
        return $user;
    }

}
