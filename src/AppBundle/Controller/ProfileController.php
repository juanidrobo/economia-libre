<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProfileController extends Controller {

    public function profileAction(Request $request) {

        $session = $request->getSession();
        $userSession = $session->get("userSession");
        $renderArray = array();
        $autoEmail = $request->query->get('email');
        $autoPhone = $request->query->get('phone');
        $autoInfo = $request->query->get('info');
        $renderArray["userSession"] = $userSession;
        $renderArray["autoEmail"] = $autoEmail;
        $renderArray["autoPhone"] = $autoPhone;
        $renderArray["autoInfo"] = $autoInfo;
        return $this->render('AppBundle:profile:profile.html.twig', $renderArray);
    }

    public function getProfileAction(Request $request) {

        $response = new Response();
        $responseArray = array();

        $userJson = $request->request->get('user');
        if ($userJson === null) {
            $response->setStatusCode(400);
            $response->setContent('{"error": "null post data "}');
            return $response;
        }

        $userArray = json_decode($userJson, true);

        $user = null;

        if ($userArray['email'] != null) {
            $userInfo = $this->getDoctrine()->getRepository('AppBundle:UserInfo')->findOneBy(array("visible" => true, "type" => "email", "info" => $userArray['email']));
            if ($userInfo) {
                $user = $userInfo->getUser();
            }
        }
        if (!$user) {
            if ($userArray['phone'] != null) {
                $userInfo = $this->getDoctrine()->getRepository('AppBundle:UserInfo')->findOneBy(array("visible" => true, "type" => "phone", "info" => $userArray['phone']));
                if ($userInfo) {
                    $user = $userInfo->getUser();
                }
            }
        }
        if (!$user) {
            if (isset($userArray['info']) && $userArray['info'] != null) {
                $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($userArray['info']);
            }
        }

        if (!$user) {
            $response->setStatusCode(300);
            $response->setContent('{"error": "No se encuentra información relacionada con estos datos (correo y/o telefono)."}');
            return $response;
        }


        $responseArray['displayName'] = $user->getDisplayName();
        // to get all the user info data from user
        $userInfo = $this->getDoctrine()->getRepository('AppBundle:UserInfo')->findBy(array("user" => $user));
        $userInfoArray = array();
        $i = 0;
        foreach ($userInfo as $ui) {
            if ($ui->getInfo() === $user->getEmail()) {
                if (!$ui->getVisible() || !$ui->getActive()) {
                    $user->setEmail(null);
                }
            }
            if ($ui->getInfo() === $user->getPhone()) {
                if (!$ui->getVisible() || !$ui->getActive()) {
                    $user->setPhone(null);
                }
            }
            if ($ui->getActive() && $ui->getVisible() && $ui->getType() === "email" && $ui->getInfo() !== $user->getEmail()) {
                $userInfoArray[$i] = array($ui->getType(), $ui->getInfo());
                $i++;
            }
            if ($ui->getActive() && $ui->getVisible() && $ui->getType() === "phone" && $ui->getInfo() !== $user->getPhone()) {
                $userInfoArray[$i] = array($ui->getType(), $ui->getInfo());
                $i++;
            }
        }


        $userNetworks = $this->getDoctrine()->getRepository('AppBundle:Network')->findBy(array("user" => $user, "visible" => true));
        $userNetworksArray = array();
        foreach ($userNetworks as $network) {
            $networkArray = array();
            $networkArray['displayUrl'] = $network->getDisplayUrl();
            $networkArray['displayName'] = $network->getDisplayName();
            $networkArray['userName'] = $network->getUserName();
            $networkArray['userNameVerified'] = $network->getUserNameVerified();
            $networkArray['name'] = $network->getName();
            array_push($userNetworksArray, $networkArray);
        }


        $responseArray['userInfo'] = $userInfoArray;
        $responseArray['userEmail'] = $user->getEmail();
        $responseArray['userPhone'] = $user->getPhone();
        $responseArray['userNetworks'] = $userNetworksArray;

        $createdPromises = $this->getDoctrine()->getRepository('AppBundle:Profile')->getCreatedPromises($user->getCode(), 0);
        $writtenReviews = $this->getDoctrine()->getRepository('AppBundle:Profile')->getWrittenReviews($user->getCode(), 0);

        /*     if (empty($createdPromises) && empty($writtenReviews)) {
          $response->setStatusCode(300);
          $response->setContent('{"error": "El usuario no ha creado, ni utilizado monedas. O las monedas creadas no son visibles."}');
          return $response;
          }
         */
        $receivedReviews = $this->getDoctrine()->getRepository('AppBundle:Profile')->getReceivedReviews($user->getCode(), 0);


        $responseArray['success'] = "listo";
        $responseArray['createdPromises'] = $createdPromises;
        $responseArray['receivedReviews'] = $receivedReviews;
        $responseArray['writtenReviews'] = $writtenReviews;


        if (count($createdPromises) < $this->getDoctrine()->getRepository('AppBundle:Profile')->getNumberOfResults()) {
            $responseArray['nextCreatedPromises'] = "no-more";
        } else {
            $responseArray['nextCreatedPromises'] = "more";
        }


        if (count($receivedReviews) < $this->getDoctrine()->getRepository('AppBundle:Profile')->getNumberOfResults()) {
            $responseArray['nextReceivedReviews'] = "no-more";
        } else {
            $responseArray['nextReceivedReviews'] = "more";
        }

        if (count($writtenReviews) < $this->getDoctrine()->getRepository('AppBundle:Profile')->getNumberOfResults()) {
            $responseArray['nextWrittenReviews'] = "no-more";
        } else {
            $responseArray['nextWrittenReviews'] = "more";
        }

        $response->setContent(json_encode($responseArray));

        return $response;
    }

    public function getMoreCreatedPromisesAction(Request $request) {

        $response = new Response();
        $responseArray = array();
        $userData = $request->request->get('user');
        if ($userData === null) {
            $response->setStatusCode(400);
            $response->setContent('{"error": "null post data "}');
            return $response;
        }

        $userJson = json_decode($userData, true);
        $userEmail = $userJson['email'];
        $offsetCreatedPromises = $userJson['offset'];

        $userInfo = $this->getDoctrine()->getRepository('AppBundle:UserInfo')->findOneBy(array("type" => "email", "info" => $userEmail));

        if ($userInfo === null) {
            $response->setStatusCode(300);
            $response->setContent('{"error": "El usuario no tiene monedas activas."}');
            return $response;
        }

        $user = $userInfo->getUser();

        $createdPromises = $this->getDoctrine()->getRepository('AppBundle:Profile')->getCreatedPromises($user->getCode(), $offsetCreatedPromises);
        $responseArray['success'] = "listo";
        $responseArray['createdPromises'] = $createdPromises;

        if (count($createdPromises) < $this->getDoctrine()->getRepository('AppBundle:Profile')->getNumberOfResults()) {
            $responseArray['nextCreatedPromises'] = "no-more";
        } else {
            $responseArray['nextCreatedPromises'] = "more";
        }


        $response->setContent(json_encode($responseArray));


        return $response;
    }

    public function getMoreReceivedReviewsAction(Request $request) {

        $response = new Response();
        $responseArray = array();
        $userData = $request->request->get('user');
        if ($userData === null) {
            $response->setStatusCode(400);
            $response->setContent('{"error": "null post data "}');
            return $response;
        }

        $userJson = json_decode($userData, true);
        $userEmail = $userJson['email'];
        $offsetReceivedReviews = $userJson['offset'];

        $userInfo = $this->getDoctrine()->getRepository('AppBundle:UserInfo')->findOneBy(array("type" => "email", "info" => $userEmail));

        if ($userInfo === null) {
            $response->setStatusCode(300);
            $response->setContent('{"error":"El usuario no tiene monedas activas."}');
            return $response;
        }
        $user = $userInfo->getUser();


        $receivedReviews = $this->getDoctrine()->getRepository('AppBundle:Profile')->getReceivedReviews($user->getCode(), $offsetReceivedReviews);
        $responseArray['success'] = "listo";
        $responseArray['receivedReviews'] = $receivedReviews;

        if (count($receivedReviews) < $this->getDoctrine()->getRepository('AppBundle:Profile')->getNumberOfResults()) {
            $responseArray['nextReceivedReviews'] = "no-more";
        } else {
            $responseArray['nextReceivedReviews'] = "more";
        }


        $response->setContent(json_encode($responseArray));



        return $response;
    }

    public function getMoreWrittenReviewsAction(Request $request) {

        $response = new Response();
        $responseArray = array();
        $userData = $request->request->get('user');
        if ($userData === null) {
            $response->setStatusCode(400);
            $response->setContent('{"error": "null post data "}');
            return $response;
        }

        $userJson = json_decode($userData, true);
        $userEmail = $userJson['email'];
        $offsetWrittenReviews = $userJson['offset'];

        $userInfo = $this->getDoctrine()->getRepository('AppBundle:UserInfo')->findOneBy(array("type" => "email", "info" => $userEmail));

        if ($userInfo === null) {
            $response->setStatusCode(300);
            $response->setContent('{"error": "El usuario no tiene monedas activas."}');
            return $response;
        }
        $user = $userInfo->getUser();


        $writtenReviews = $this->getDoctrine()->getRepository('AppBundle:Profile')->getWrittenReviews($user->getCode(), $offsetWrittenReviews);
        $responseArray['success'] = "listo";
        $responseArray['writtenReviews'] = $writtenReviews;

        if (count($writtenReviews) < $this->getDoctrine()->getRepository('AppBundle:Profile')->getNumberOfResults()) {
            $responseArray['nextWrittenReviews'] = "no-more";
        } else {
            $responseArray['nextWrittenReviews'] = "more";
        }


        $response->setContent(json_encode($responseArray));



        return $response;
    }

}
