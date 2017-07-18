<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HistoryController extends Controller {

    public function historyAction(Request $request) {
        $session = $request->getSession();
        $userSession = $session->get("userSession");
        $renderArray = Array();
        $renderArray['userSession'] = $userSession;
        $renderArray['csrf'] = uniqid("", true);
        return $this->render('AppBundle:history:history.html.twig', $renderArray);
    }

    public function getHistoryAction(Request $request) {

        $session = $request->getSession();
        $userSession = $session->get("userSession");
        $response = new Response();
        $responseArray = array();


        if ($userSession) {

            $createdPromises = $this->getDoctrine()->getRepository('AppBundle:History')->getCreatedPromises($userSession->getCode(), 0);
            $writtenReviews = $this->getDoctrine()->getRepository('AppBundle:History')->getWrittenReviews($userSession->getCode(), 0);
            $ownPromises = $this->getDoctrine()->getRepository('AppBundle:History')->getOwnPromises($userSession->getCode(), 0);



            $receivedReviews = $this->getDoctrine()->getRepository('AppBundle:History')->getReceivedReviews($userSession->getCode(), 0);


            $responseArray['success'] = "listo";
            $responseArray['createdPromises'] = $createdPromises;
            $responseArray['ownPromises'] = $ownPromises;
            $responseArray['receivedReviews'] = $receivedReviews;
            $responseArray['writtenReviews'] = $writtenReviews;


            if (count($createdPromises) < $this->getDoctrine()->getRepository('AppBundle:History')->getNumberOfResults()) {
                $responseArray['nextCreatedPromises'] = "no-more";
                $responseArray['countCreatedPromises'] = count($createdPromises);
            } else {
                $responseArray['nextCreatedPromises'] = "more";
                $responseArray['countCreatedPromises'] = count($createdPromises) . "+";
            }

            if (count($ownPromises) < $this->getDoctrine()->getRepository('AppBundle:History')->getNumberOfResults()) {
                $responseArray['nextOwnPromises'] = "no-more";
                $responseArray['countOwnPromises'] = count($ownPromises);
            } else {
                $responseArray['nextOwnPromises'] = "more";
                $responseArray['countOwnPromises'] = count($ownPromises) . "+";
            }

            if (count($receivedReviews) < $this->getDoctrine()->getRepository('AppBundle:History')->getNumberOfResults()) {
                $responseArray['nextReceivedReviews'] = "no-more";
                $responseArray['countReceivedReviews'] = count($receivedReviews);
            } else {
                $responseArray['nextReceivedReviews'] = "more";
                $responseArray['countReceivedReviews'] = count($receivedReviews) . "+";
            }

            if (count($writtenReviews) < $this->getDoctrine()->getRepository('AppBundle:History')->getNumberOfResults()) {
                $responseArray['nextWrittenReviews'] = "no-more";
                $responseArray['countWrittenReviews'] = count($writtenReviews);
            } else {
                $responseArray['nextWrittenReviews'] = "more";
                $responseArray['countWrittenReviews'] = count($writtenReviews) . "+";
            }

            $response->setContent(json_encode($responseArray));
        } else {
            $response->setStatusCode(300);
            $response->setContent('{"error": "No has iniciado sesión."}');
        }

        return $response;
    }

    public function getMoreCreatedPromisesAction(Request $request) {
        $session = $request->getSession();
        $userSession = $session->get("userSession");

        $response = new Response();
        $responseArray = array();

        if ($userSession) {
            $offsetCreatedPromises = $request->request->get('offset');

            $createdPromises = $this->getDoctrine()->getRepository('AppBundle:History')->getCreatedPromises($userSession->getCode(), $offsetCreatedPromises);
            $responseArray['success'] = "listo";
            $responseArray['createdPromises'] = $createdPromises;


            if (count($createdPromises) < $this->getDoctrine()->getRepository('AppBundle:History')->getNumberOfResults()) {
                $responseArray['nextCreatedPromises'] = "no-more";
            } else {
                $responseArray['nextCreatedPromises'] = "more";
            }

            $response->setContent(json_encode($responseArray));
        } else {
            $response->setStatusCode(300);
            $response->setContent('{"error": "No has iniciado sesión."}');
        }

        return $response;
    }

    public function getMoreOwnPromisesAction(Request $request) {
        $session = $request->getSession();
        $userSession = $session->get("userSession");
        $response = new Response();
        $responseArray = array();
        if ($userSession) {
            $offsetOwnPromises = $request->request->get('offset');
            $ownPromises = $this->getDoctrine()->getRepository('AppBundle:History')->getOwnPromises($userSession->getCode(), $offsetOwnPromises);
            $responseArray['success'] = "listo";
            $responseArray['ownPromises'] = $ownPromises;

            if (count($ownPromises) < $this->getDoctrine()->getRepository('AppBundle:History')->getNumberOfResults()) {
                $responseArray['nextOwnPromises'] = "no-more";
            } else {
                $responseArray['nextOwnPromises'] = "more";
            }

            $response->setContent(json_encode($responseArray));
        } else {
            $response->setStatusCode(300);
            $response->setContent('{"error": "No has iniciado sesión."}');
        }


        return $response;
    }

    public function getMoreReceivedReviewsAction(Request $request) {

        $session = $request->getSession();
        $userSession = $session->get("userSession");
        $response = new Response();
        $responseArray = array();
        if ($userSession) {
            $offsetReceivedReviews = $request->request->get('offset');

            $receivedReviews = $this->getDoctrine()->getRepository('AppBundle:History')->getReceivedReviews($userSession->getCode(), $offsetReceivedReviews);
            $responseArray['success'] = "listo";
            $responseArray['receivedReviews'] = $receivedReviews;

            if (count($receivedReviews) < $this->getDoctrine()->getRepository('AppBundle:History')->getNumberOfResults()) {
                $responseArray['nextReceivedReviews'] = "no-more";
            } else {
                $responseArray['nextReceivedReviews'] = "more";
            }


            $response->setContent(json_encode($responseArray));
        } else {
            $response->setStatusCode(300);
            $response->setContent('{"error": "No has iniciado sesión."}');
        }


        return $response;
    }

    public function getMoreWrittenReviewsAction(Request $request) {


        $session = $request->getSession();
        $userSession = $session->get("userSession");
        $response = new Response();
        $responseArray = array();
        if ($userSession) {
            $offsetWrittenReviews = $request->request->get('offset');

            $writtenReviews = $this->getDoctrine()->getRepository('AppBundle:History')->getWrittenReviews($userSession->getCode(), $offsetWrittenReviews);
            $responseArray['success'] = "listo";
            $responseArray['writtenReviews'] = $writtenReviews;


            if (count($writtenReviews) < $this->getDoctrine()->getRepository('AppBundle:History')->getNumberOfResults()) {
                $responseArray['nextWrittenReviews'] = "no-more";
            } else {
                $responseArray['nextWrittenReviews'] = "more";
            }


            $response->setContent(json_encode($responseArray));
        } else {
            $response->setStatusCode(300);
            $response->setContent('{"error": "No has iniciado sesión."}');
        }


        return $response;
    }

    public function toggleAction(Request $request) {

        $session = $request->getSession();
        $userSession = $session->get("userSession");
        $response = new Response();
        $responseArray = array();
        if ($userSession) {
            $promiseCode = $request->request->get('promise');

            $promise = $this->getDoctrine()->getRepository('AppBundle:Promise')->find($promiseCode);
            if ($promise === null) {
                $response->setStatusCode(300);
                $response->setContent('{"error": "No existe la moneda."}');
                return $response;
            }

            $event = $this->getDoctrine()->getRepository('AppBundle:Event')->findOneBy(array("promise" => $promiseCode), array("date" => "DESC"));
            if (empty($event)) {
                $response->setStatusCode(300);
                $response->setContent('{"error": "Error - No se puede modificar la visibilidad de esta moneda."}');
                return $response;
            }

            if ($event->getAction() === "claim" || $event->getAction() === "review") {
                $response->setStatusCode(300);
                $response->setContent('{"error": "No se puede modificar la visibilidad de esta moneda."}');
                return $response;
            }

            if ($promise->getVisible()) {
                $promise->setVisible(false);
            } else {
                $promise->setVisible(true);
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($promise);
            $em->flush();
            $responseArray['success'] = "listo";


            $response->setContent(json_encode($responseArray));
        } else {
            $response->setStatusCode(300);
            $response->setContent('{"error": "No has iniciado sesión."}');
        }


        return $response;
    }

}
