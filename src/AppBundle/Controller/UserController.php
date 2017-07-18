<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use AppBundle\Entity\User;
use AppBundle\Entity\UserInfo;
use AppBundle\Entity\Network;
use Abraham\TwitterOAuth\TwitterOAuth;
use Abraham\TwitterOAuth\TwitterOAuthException;

class UserController extends Controller {

    //This action only old school own register  (no social network, or login kit)
    public function registerAction(Request $request) {
        $responseArray = array();
        $response = new Response();
        $userJSON = $request->request->get('user');
        if ($userJSON === null) {
            $response->setStatusCode(400);
            $response->setContent('{"error": null post data "}');
            return $response;
        }
        $userArray = json_decode($userJSON, true);
        if (isset($userArray['email'])) {
            $email = $userArray['email'];
        } else {
            $email = null;
        }
        if (isset($userArray['phone'])) {
            $phone = $userArray['phone'];
        } else {
            $phone = null;
        }
        if (isset($userArray['name'])) {
            $name = $userArray['name'];
        } else {
            $name = null;
        }

        $em = $this->getDoctrine()->getManager();
        $userInfo = $this->getDoctrine()->getRepository('AppBundle:UserInfo')->findOneBy(array("type" => "email", "info" => $email));

        if ($userInfo) {
            $user = $userInfo->getUser();
            $user->setVerification(uniqid("", true));
            $em->persist($user);
            $em->flush();
        } else {
            $user = new User();
            $user->setEmail($email);
            $user->setPhone($phone);
            $user->setName($name);
            $user->setActive(false);
            $user->setVerification(uniqid("", true));
            $em->persist($user);
            $em->flush();
            $userInfo = new UserInfo();
            $userInfo->setType("email");
            $userInfo->setInfo($email);
            $userInfo->setUser($user);
            $userInfo->setactive(false);
            $userInfo->setvisible(true);
            $em->persist($userInfo);
            $em->flush();
        }
        if ($user->getEmail()) {
            $mailer = $this->get('app.mailer');
            $template = $this->renderView("emails/activate-user.html.twig", array('user' => $user));
            $mailer->sendEmail("Activar usuario", $user->getEmail(), $template);
            $responseArray["success"] = "Un correo ha sido enviado para activar el usuario.";
        }


        $response->setContent(json_encode($responseArray));
        return $response;
    }

    public function activateUserAction(Request $request, $code) {
        $session = $request->getSession();
        $errorSocialLogin = $session->getFlashBag()->get('errorSocialLogin');


        $renderArray = Array();
        if ($errorSocialLogin)
            $renderArray["errorSocialLogin"] = $errorSocialLogin[0];

        $renderArray['userSession'] = null;

        if (strpos($code, '&')) {
            $keywords = preg_split("/[&]/", $code);
            $code = $keywords[0];
            $verification = $keywords[1];
            $email = $keywords[2];
        }

        $em = $this->getDoctrine()->getManager();
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($code);
        if ($user === null) {
            return $this->render('AppBundle:user:activate-error.html.twig', $renderArray);
        }

        if ($user->getActive() === false) {
            $session->set("userSession", null);
            $renderArray['user'] = $user;
            return $this->render('AppBundle:user:create-key.html.twig', $renderArray);
        } else {
            $session->set("userSession", $user);
            return $this->redirectToRoute('home', array('userSession' => $user));
        }
    }

    public function loginAction(Request $request) {
        $responseArray = array();
        $response = new Response();
        $userJson = $request->request->get('user');
        if ($userJson === null) {
            $response->setStatusCode(400);
            $response->setContent('{"error": null post data "}');
            return $response;
        }
        $userArray = json_decode($userJson, true);
        $email = $userArray['email'];
        $seckey = $userArray['seckey'];

        $em = $this->getDoctrine()->getManager();
        $userInfo = $this->getDoctrine()->getRepository('AppBundle:UserInfo')->findOneBy(array("type" => "email", "info" => $email));
        if ($userInfo) {

            $user = $userInfo->getUser();
            if ($user->getSecKey() === $seckey) {
                $responseArray['success'] = "indentified";
                $session = $request->getSession();
                $session->set("userSession", $user);
            } else {
                $response->setStatusCode(300);
                $responseArray["error"] = "Identificación incorrecta!";
            }
        } else {
            $response->setStatusCode(300);
            $responseArray["error"] = "Identificación incorrecta!";
        }
        $response->setContent(json_encode($responseArray));
        return $response;
    }

    public function logoutAction(Request $request) {
        $url = $request->headers->get('referer');
        $session = $request->getSession();
        $session->remove("userSession");
        return $this->redirect($url);
    }

    public function createSecKeyAction(Request $request) {
        $response = new Response();
        $userJson = $request->request->get('user');
        if ($userJson === null) {
            $response->setStatusCode(400);
            $response->setContent('{"error": null post data "}');
            return $response;
        }
        $userArray = json_decode($userJson, true);
        $userCode = $userArray['code'];
        $seckey = $userArray['seckey'];

        $em = $this->getDoctrine()->getManager();
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($userCode);

        $user->setSecKey($seckey);
        $user->setActive(true);
        $em->persist($user);
        $userInfo = $this->getDoctrine()->getRepository('AppBundle:UserInfo')->findOneBy(array("type" => "email", "info" => $user->getEmail()));
        $userInfo->setActive(true);
        $em->persist($userInfo);
        $em->flush();

        $session = $request->getSession();
        $session->set("userSession", $user);

        $response->setContent('{"success":"El usuario ahora tiene contraseña."}');
        return $response;
    }

    public function validateUserAction(Request $request) {
        $response = new Response();
        $userJson = $request->request->get('user');

        if ($userJson === null) {
            $response->setStatusCode(400); //bad request
            $response->setContent('{"error":  post data es nulo "}');

            return $response;
        }
        $userArray = json_decode($userJson, true);
        $email = $userArray['email'];
        $userInfo = $this->getDoctrine()->getRepository('AppBundle:UserInfo')->findOneBy(array("type" => "email", "info" => $email));

        if ($userInfo) {
            $user = $userInfo->getUser();
            if ($user->getSecKey() !== null && $user->getSecKey() !== "") {
                $response->setContent('{"success":"Usuario existe y tiene llave."}');
            } else {

                $response->setStatusCode(300);
                $response->setContent('{"error":"El usuario no tiene contraseña."}');
            }
        } else {
            $response->setStatusCode(300);
            $response->setContent('{"error":"El usuario no existe."}');
        }

        return $response;
    }

    public function recoveryKeyAction(Request $request) {
        $session = $request->getSession();
        $userSession = $session->get("userSession");

        return $this->render('AppBundle:user:recovery-key.html.twig', array("userSession" => $userSession));
    }

    public function notifyNewKeyAction(Request $request) {

        $response = new Response();
        $userJson = $request->request->get('user');
        if ($userJson === null) {
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
        $captchaData = $reCaptcha->validateCaptcha($captcha);
        $captchaJson = json_decode($captchaData, true);
        if ($captchaJson['success'] === false) {
            $response->setStatusCode(300);
            $response->setContent('{"error": "Error en el captcha."}');
            return $response;
        }

        $userArray = json_decode($userJson, true);
        $userEmail = $userArray['email'];

        $userInfo = $this->getDoctrine()->getRepository('AppBundle:UserInfo')->findOneBy(array("type" => "email", "info" => $userEmail));

        if ($userInfo === null) {
            $response->setStatusCode(300);
            $response->setContent('{"error": "El correo no existe, crea una nueva moneda para continuar."}');
            return $response;
        }
        $user = $userInfo->getUser();

        $em = $this->getDoctrine()->getManager();
        $user->setVerification(uniqid("", true));
        $em->persist($user); // to change the verification code
        $em->flush();

        $mailer = $this->get('app.mailer');
        $template = $this->renderView("emails/user-new-key.html.twig", array('user' => $user));
        $mailer->sendEmail("Recupera tu contraseña", $userEmail, $template);

        $response->setContent('{"success":"Un correo ha sido enviado para crear una nueva contraseña."}');
        return $response;
    }

    public function newKeyAction(Request $request, $code) {
        $session = $request->getSession();
        $errorSocialLogin = $session->getFlashBag()->get('errorSocialLogin');
        $session->remove("userSession");
        $renderArray = Array();
        if ($errorSocialLogin)
            $renderArray["errorSocialLogin"] = $errorSocialLogin[0];

        $renderArray["userSession"] = null;
        //if code contains '&'
        if (strpos($code, '&')) {
            $keywords = preg_split("/[&]/", $code);
            $userCode = $keywords[0];
            $verification = $keywords[1];
        } else {
            return $this->render('AppBundle:user:new-key-error.html.twig');
        }

        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($userCode);
        if ($user === null) {
            return $this->render('AppBundle:user:new-key-error.html.twig', $renderArray);
        }

        if ($user->getVerification() === $verification) {
            $renderArray['user'] = $user;
            $renderArray['code'] = $code;
            return $this->render('AppBundle:user:new-key.html.twig', $renderArray);
        } else {
            return $this->render('AppBundle:user:new-key-error.html.twig', $renderArray);
        }
    }

    public function changeKeyAction(Request $request) {

        $response = new Response();
        $userJson = $request->request->get('user');
        if ($userJson === null) {
            $response->setStatusCode(400);
            $response->setContent('{"error": "null post data "}');
            return $response;
        }

        $userArray = json_decode($userJson, true);
        $userCode = $userArray['code'];
        $newSecKey = $userArray['seckey'];
        $verification = $userArray['verification'];

        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($userCode);
        if ($user === null) {
            $response->setStatusCode(300);
            $response->setContent('{"error": "Error cambiando la contraseña."}');
            return $response;
        }

        if ($user->getVerification() === $verification) {
            $user->setSecKey($newSecKey);
            $user->setActive(true);
            $user->setVerification(uniqid("", true));
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $session = $request->getSession();
            $session->set("userSession", $user);

            $response->setContent('{"success": "Contraseña cambiada exitosamente!"}');
        } else {
            $response->setStatusCode(300);
            $response->setContent('{"error": "' . $verification . '"}');
            return $response;
        }

        return $response;
    }

    public function userInfoAction(Request $request) {
        $response = new Response();
        $session = $request->getSession();
        $userSession = $session->get("userSession");
        $renderArray = Array();
        $renderArray['userSession'] = $userSession;

        $renderArray['csrf'] = uniqid("", true);
        if ($userSession) {
            $userInfo = $this->getDoctrine()->getRepository('AppBundle:UserInfo')->findBy(array("user" => $userSession->getCode()));
            $renderArray['userInfo'] = $userInfo;
            $userNetworks = $this->getDoctrine()->getRepository('AppBundle:Network')->findBy(array("user" => $userSession->getCode()));
            $validUserNetworks = array();
            foreach ($userNetworks as $network) {
                if ($network->getName() !== "afk") {
                    array_push($validUserNetworks, $network);
                }
            }
            $renderArray['userNetworks'] = $validUserNetworks;
        }
        $error = $session->getFlashBag()->get('errorAddSocialInfo');
        if ($error) {
            $renderArray['error'] = $error[0];
        }
        $success = $session->getFlashBag()->get('successAddSocialInfo');
        if ($success) {
            $renderArray['success'] = $success[0];
        }
        $renderArray['fb_client_page'] = $this->container->getParameter('fb_client_page');

        return $this->render('AppBundle:user:user-info.html.twig', $renderArray);
    }

    public function editUserAction(Request $request) {

        $response = new Response();
        $session = $request->getSession();
        $userSession = $session->get("userSession");
        $em = $this->getDoctrine()->getManager();
        $responseArray = Array();

        if ($userSession) {
            $response = new Response();
            $userJson = $request->request->get('user');
            if ($userJson === null) {
                $response->setStatusCode(400);
                $response->setContent('{"error": "null post data "}');
                return $response;
            }

            $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($userSession->getCode());

            $userArray = json_decode($userJson, true);

            if (isset($userArray['name']) && $userArray['name'] !== "") {
                $user->setName($userArray['name']);
            }
            //to verify the  email its not corrupted in the middle, javascript hacked code.
            if (isset($userArray['email'])) {
                $userInfo = $this->getDoctrine()->getRepository('AppBundle:UserInfo')->findOneBy(array('type' => 'email', 'info' => $userArray['email'], 'user' => $user));
                if ($userInfo) {
                    $user->setEmail($userArray['email']);
                } else {
                    $response->setStatusCode(300);
                    $responseArray['error'] = "Toda la información es confirmada, si quieres agregar un nuevo dato hazlo por medio de la interfaz. Si logras agregar información que no es confirmada, dejanos saber y asi mejoramos la seguridad! Un abrazo!";
                    $response->setContent(json_encode($responseArray));
                    return $response;
                }
            }

            //to verify the phone its not corrupted in the middle, javascript hacked code.
            if (isset($userArray['phone'])) {
                $userInfo = $this->getDoctrine()->getRepository('AppBundle:UserInfo')->findOneBy(array('type' => 'phone', 'info' => $userArray['phone'], 'user' => $user));
                if ($userInfo) {
                    $user->setPhone($userArray['phone']);
                } else {
                    $response->setStatusCode(300);
                    $responseArray['error'] = "Toda la información es confirmada, si quieres agregar un nuevo dato hazlo por medio de la interfaz. Si logras agregar información que no es confirmada, dejanos saber y asi mejoramos la seguridad! Un abrazo!";
                    $response->setContent(json_encode($responseArray));
                    return $response;
                }
            }

            //Info to remove, sadly :( but it's people's choice.
            $countInfoRemoved = 0;

            for ($i = 0; $i < count($userArray['infoToRemove']); $i++) {
                $userInfo = $this->getDoctrine()->getRepository('AppBundle:UserInfo')->findOneBy(array('info' => $userArray['infoToRemove'][$i], 'user' => $user));
                if ($userInfo) {
                    // to prevent deleting primary info
                    if ($userSession->getEmail() !== $userInfo->getInfo() && $userSession->getPhone() !== $userInfo->getInfo()) {
                        $em->remove($userInfo);
                        $countInfoRemoved++;
                    }
                } else {
                    $response->setStatusCode(300);
                    $responseArray['error'] = "Toda la información es confirmada, si quieres agregar un nuevo dato hazlo por medio de la interfaz. Si logras agregar información que no es confirmada, dejanos saber y asi mejoramos la seguridad! Un abrazo!";
                    $response->setContent(json_encode($responseArray));
                    return $response;
                }
            }


            //Info visible/not visible
            for ($i = 0; $i < count($userArray['infoToToggle']); $i++) {
                $userInfo = $this->getDoctrine()->getRepository('AppBundle:UserInfo')->findOneBy(array('info' => $userArray['infoToToggle'][$i], 'user' => $user));
                if ($userInfo) {
                    if ($userInfo->getVisible()) {
                        $userInfo->setVisible(false);
                    } else {
                        $userInfo->setVisible(true);
                    }
                    $em->persist($userInfo);
                } else {
                    $response->setStatusCode(300);
                    $responseArray['error'] = "Toda la información es confirmada, si quieres agregar un nuevo dato hazlo por medio de la interfaz. Si logras agregar información que no es confirmada, dejanos saber y asi mejoramos la seguridad! Un abrazo!";
                    $response->setContent(json_encode($responseArray));
                    return $response;
                }
            }

            //Network visible/not visible
            for ($i = 0; $i < count($userArray['networkToToggle']); $i++) {
                $network = $this->getDoctrine()->getRepository('AppBundle:Network')->findOneBy(array('code' => $userArray['networkToToggle'][$i], 'user' => $user));
                if ($network) {
                    if ($network->getVisible()) {
                        $network->setVisible(false);
                    } else {
                        $network->setVisible(true);
                    }
                    $em->persist($network);
                } else {
                    $response->setStatusCode(300);
                    $responseArray['error'] = "Toda la información es confirmada, si quieres agregar un nuevo dato hazlo por medio de la interfaz. Si logras agregar información que no es confirmada, dejanos saber y asi mejoramos la seguridad! Un abrazo!";
                    $response->setContent(json_encode($responseArray));
                    return $response;
                }
            }

            //fb special case, because fb doest share the username, so we CANT know the user profile url
            if (isset($userArray['fbGivenUsername'])) {
                //it has to be unique by logic not by database constraints
                $network = $this->getDoctrine()->getRepository('AppBundle:Network')->findOneBy(array('name' => 'fb', 'user' => $user));
                if ($network) {
                    if ($network->getUserName() !== $userArray['fbGivenUsername']) {
                        if ($userArray['fbGivenUsername'] !== "") {
                            $network->setUserName($userArray['fbGivenUsername']);
                            $em->persist($network);
                        }
                    }
                } else {
                    $response->setStatusCode(300);
                    $responseArray['error'] = "Toda la información es confirmada, si quieres agregar un nuevo dato hazlo por medio de la interfaz. Si logras agregar información que no es confirmada, dejanos saber y asi mejoramos la seguridad! Un abrazo!";
                    $response->setContent(json_encode($responseArray));
                    return $response;
                }
            }


            // Check if is possible to delete networks
            $deleteNetworks = false;

            if (count($userArray['networkToRemove']) > 0) {
                $userNetworks = $this->getDoctrine()->getRepository('AppBundle:Network')->findBy(array('user' => $user));

                $userInfo = $this->getDoctrine()->getRepository('AppBundle:UserInfo')->findBy(array('user' => $user));
                // if there are user info after deleting
                if ($countInfoRemoved < count($userInfo)) {
                    if ($userSession->getSecKey() != null) {
                        $deleteNetworks = true;
                    } else {
                        if (count($userArray['networkToRemove']) < count($userNetworks)) {
                            $deleteNetworks = true;
                        } else {
                            $response->setStatusCode(300);
                            $error = "Este usuario existe porque tiene algún dato verificado. Correo, telefono o red social.";
                            if (!$userSession->getSecKey()) {
                                $error = $error . " Si eliminas las redes sociales debes de indicar una contraseña. un abrazo!";
                            }

                            $responseArray['error'] = $error;
                            $response->setContent(json_encode($responseArray));
                            return $response;
                        }
                    }
                } else {

                    if (count($userArray['networkToRemove']) < count($userNetworks)) {
                        $deleteNetworks = true;
                    } else {
                        $response->setStatusCode(300);
                        $error = "Este usuario existe porque tiene algún dato verificado. Correo, telefono o red social.";
                        if (!$userSession->getSecKey()) {
                            $error = $error . " Si eliminas las redes sociales debes de indicar una contraseña. un abrazo!";
                        }

                        $responseArray['error'] = $error;
                        $response->setContent(json_encode($responseArray));
                        return $response;
                    }
                }
            }

            if ($deleteNetworks) {
                for ($i = 0; $i < count($userArray['networkToRemove']); $i++) {
                    $network = $this->getDoctrine()->getRepository('AppBundle:Network')->find($userArray['networkToRemove'][$i]);
                    if ($network) {
                        $token = $network->getToken();
                        if ($network->getName() == "fb") {
                            $options = array(
                                CURLOPT_CUSTOMREQUEST => "DELETE",
                                CURLOPT_RETURNTRANSFER => true, // return web page
                                CURLOPT_HEADER => false, // don't return headers
                                CURLOPT_FOLLOWLOCATION => true, // follow redirects
                                CURLOPT_MAXREDIRS => 10, // stop after 10 redirects
                                CURLOPT_ENCODING => "", // handle compressed
                                CURLOPT_USERAGENT => "", // name of client
                                CURLOPT_AUTOREFERER => true, // set referrer on redirect
                                CURLOPT_CONNECTTIMEOUT => 120, // time-out on connect
                                CURLOPT_TIMEOUT => 120, // time-out on response
                            );
                            $ch = curl_init($this->container->getParameter('fb_graph_url') . $network->getNetworkCode() . '/permissions/?access_token=' . $token);
                            curl_setopt_array($ch, $options);
                            $content = curl_exec($ch);
                            $responseArray['info'] = $content;
                            curl_close($ch);
                        }
                        if ($network->getName() == "g") {
                            $options = array(
                                CURLOPT_RETURNTRANSFER => true, // return web page
                                CURLOPT_HEADER => false, // don't return headers
                                CURLOPT_FOLLOWLOCATION => true, // follow redirects
                                CURLOPT_MAXREDIRS => 10, // stop after 10 redirects
                                CURLOPT_ENCODING => "", // handle compressed
                                CURLOPT_USERAGENT => "", // name of client
                                CURLOPT_AUTOREFERER => true, // set referrer on redirect
                                CURLOPT_CONNECTTIMEOUT => 120, // time-out on connect
                                CURLOPT_TIMEOUT => 120, // time-out on response
                            );
                            $ch = curl_init($this->container->getParameter('g_revoke_url') . '?token=' . $token);
                            curl_setopt_array($ch, $options);
                            $content = curl_exec($ch);
                            $responseArray['info'] = $content;
                            curl_close($ch);
                        }

                        $em->remove($network);
                    } else {
                        $response->setStatusCode(300);
                        $responseArray['error'] = "Toda la información es confirmada, si quieres agregar un nuevo dato hazlo por medio de la interfaz. Si logras agregar información que no es confirmada, dejanos saber y asi mejoramos la seguridad! Un abrazo!";
                        $response->setContent(json_encode($responseArray));
                        return $response;
                    }
                }
            }

            $em->persist($user);
            $em->flush();


            $session->set("userSession", $user);
            $responseArray['success'] = "Se han guardado los cambíos!";
            //$otherEmails = $userArray['other-emails'];
            //$otherPhones = $userArray['other-phones'];
        } else {
            $response->setStatusCode(300);
            $responseArray['error'] = "No ha inicido sesión.";
        }
        $response->setContent(json_encode($responseArray));
        return $response;
    }

    public function fbLoginAction(Request $request) {
        $responseArray = array();
        $response = new Response();

        $userJson = $request->request->get('user');
        if ($userJson === null) {
            $response->setStatusCode(400);
            $response->setContent('{"error": null post data "}');
            return $response;
        }
        $userArray = json_decode($userJson, true);

        $options = array(
            CURLOPT_RETURNTRANSFER => true, // return web page
            CURLOPT_HEADER => false, // don't return headers
            CURLOPT_FOLLOWLOCATION => true, // follow redirects
            CURLOPT_MAXREDIRS => 10, // stop after 10 redirects
            CURLOPT_ENCODING => "", // handle compressed
            CURLOPT_USERAGENT => "", // name of client
            CURLOPT_AUTOREFERER => true, // set referrer on redirect
            CURLOPT_CONNECTTIMEOUT => 120, // time-out on connect
            CURLOPT_TIMEOUT => 120, // time-out on response
        );

        $ch = curl_init($this->container->getParameter('fb_token_exchange_url') . '?grant_type=fb_exchange_token&client_id=' . $this->container->getParameter('fb_client_id') . '&client_secret=' . $this->container->getParameter('fb_client_secret') . '&fb_exchange_token=' . $userArray['access_token']);
        curl_setopt_array($ch, $options);
        $content = curl_exec($ch);
        curl_close($ch);
        $content = json_decode($content, true);
        $token = $content['access_token'];
        $ch = curl_init($this->container->getParameter('fb_me_url') . '&access_token=' . $token);
        curl_setopt_array($ch, $options);
        $content = curl_exec($ch);
        curl_close($ch);

        $fbUserArray = json_decode($content, true);


        $socialLoginArray = array();

        $socialLoginArray['name'] = "fb";
        $socialLoginArray['token'] = $token;
        $fbId = $fbUserArray['id'];
        $socialLoginArray['id'] = $fbId;
        if (isset($fbUserArray['email'])) {
            $fbEmail = $fbUserArray['email'];
        } else {
            $fbEmail = null;
        }
        $socialLoginArray['email'] = $fbEmail;
        if (isset($fbUserArray['name'])) {
            $fbName = $fbUserArray['name'];
        } else {
            $fbName = null;
        }
        $socialLoginArray['userName'] = $fbName;
        $session = $request->getSession();
        $network = $this->getDoctrine()->getRepository('AppBundle:Network')->findOneBy(array("name" => "fb", "networkCode" => $fbId));

        $session = $request->getSession();
        $userSession = $session->get("userSession");
        if ($userSession)
            return $this->socialAddInfo($request, $socialLoginArray);
        else
            return $this->socialLogin($request, $userArray, $socialLoginArray);
    }

    public function gLoginAction(Request $request) {

        $userJson = $request->request->get('user');
        if ($userJson === null) {
            $response->setStatusCode(400);
            $response->setContent('{"error": null post data "}');
            return $response;
        }
        $userArray = json_decode($userJson, true);

        $options = array(
            CURLOPT_RETURNTRANSFER => true, // return web page
            CURLOPT_HEADER => false, // don't return headers
            CURLOPT_FOLLOWLOCATION => true, // follow redirects
            CURLOPT_MAXREDIRS => 10, // stop after 10 redirects
            CURLOPT_ENCODING => "", // handle compressed
            CURLOPT_USERAGENT => "", // name of client
            CURLOPT_AUTOREFERER => true, // set referrer on redirect
            CURLOPT_CONNECTTIMEOUT => 120, // time-out on connect
            CURLOPT_TIMEOUT => 120, // time-out on response
        );

        $token = $userArray['access_token'];
        $ch = curl_init($this->container->getParameter('g_me_url') . '?access_token=' . $userArray['access_token']);
        curl_setopt_array($ch, $options);
        $content = curl_exec($ch);
        curl_close($ch);

        $gUserArray = json_decode($content, true);



        $socialLoginArray = array();
        $socialLoginArray['name'] = "g"; //google
        $socialLoginArray['token'] = $token;
        $gId = $gUserArray['metadata']['sources'][0]['id'];
        $socialLoginArray['id'] = $gId;
        if (isset($gUserArray['emailAddresses'][0]['value'])) {
            $gEmail = $gUserArray['emailAddresses'][0]['value'];
        } else {
            $gEmail = null;
        }
        $socialLoginArray['email'] = $gEmail;
        if (isset($gUserArray['names'][0]['displayName'])) {
            $gName = $gUserArray['names'][0]['displayName'];
        } else {
            $gName = null;
        }
        $socialLoginArray['userName'] = $gName;

        $gUserName = null; //try to find plus.google in the urls of the  google user
        if (isset($gUserArray['urls'])) {
            foreach ($gUserArray['urls'] as $gUrl) {


                if (isset($gUrl['value']) && strpos($gUrl['value'], "plus.google")) {
                    $gUserName = $gUrl['value'];
                    $gUserName = substr($gUserName, strpos($gUserName, "+"));
                }
            }
        }


        $socialLoginArray['networkUserName'] = $gUserName;


        $session = $request->getSession();
        $userSession = $session->get("userSession");
        if ($userSession) {

            return $this->socialAddInfo($request, $socialLoginArray);
        } else {
            return $this->socialLogin($request, $userArray, $socialLoginArray);
        }
    }

    public function twLoginAction(Request $request) {

        $eventCode = $request->query->get("eventCode");
        $userEmail = $request->query->get("email");

        /* $userArray = array();
          $userArray['eventCode'] = $eventCode;
          $userArray['email'] = $userEmail;
          $response = new Response();
          $response->setStatusCode(300);
          $response->setContent(json_encode($userArray));
          return $response;
         */
        $urlToRedirect = base64_encode($request->headers->get('referer'));

        $twApiKey = $this->container->getParameter('tw_api_key');
        $twSecretKey = $this->container->getParameter('tw_secret_key');
        $twCallbackUrl = $this->generateUrl('twLoginCallback', array(), UrlGeneratorInterface::ABSOLUTE_URL);
        if ($eventCode !== null) {
            $twCallbackUrl = $twCallbackUrl . "?urlToRedirect=" . $urlToRedirect . "&eventCode=" . $eventCode;
        } elseif ($userEmail !== null) {
            $twCallbackUrl = $twCallbackUrl . "?urlToRedirect=" . $urlToRedirect . "&email=" . $userEmail;
        } else {
            $twCallbackUrl = $twCallbackUrl . "?urlToRedirect=" . $urlToRedirect;
        }

        /*     $response = new Response();
          $response->setStatusCode(300);
          $response->setContent(json_encode($twCallbackUrl));
          return $response;

         */
        try {
            $connection = new TwitterOAuth($twApiKey, $twSecretKey);

            $request_token = $connection->oauth('oauth/request_token', array('oauth_callback' => $twCallbackUrl));
            $session = $request->getSession();

            $oauth_token = $request_token['oauth_token'];
            $oauth_token_secret = $request_token['oauth_token_secret'];

            $session->getFlashBag()->add('oauth_token', $oauth_token);
            $session->getFlashBag()->add('oauth_token_secret', $oauth_token_secret);

            return $this->redirect($this->container->getParameter("tw_authenticate_url") . "?oauth_token=" . $oauth_token);
        } catch (TwitterOAuthException $exc) {
            return $this->redirect($request->headers->get('referer'));
        }
    }

    public function twLoginCallbackAction(Request $request) {


        $twApiKey = $this->container->getParameter('tw_api_key');
        $twSecretKey = $this->container->getParameter('tw_secret_key');
        $oauthToken = $request->query->get("oauth_token");
        $oauthVerifier = $request->query->get("oauth_verifier");

        $session = $request->getSession();
        $pastOauthToken = $session->getFlashBag()->get('oauth_token')[0];
        $pastOauthTokenSecret = $session->getFlashBag()->get('oauth_token_secret')[0];

        $connection = new TwitterOAuth($twApiKey, $twSecretKey, $oauthToken, $pastOauthTokenSecret);

        $access_token = null;
        $access_token = $connection->oauth("oauth/access_token", ["oauth_verifier" => $oauthVerifier]);


        $connection = new TwitterOAuth($twApiKey, $twSecretKey, $access_token['oauth_token'], $access_token['oauth_token_secret']);


        $content = $connection->get("account/verify_credentials", array("include_email" => true));


        $socialLoginArray = array();
        $socialLoginArray['name'] = "tw";
        $socialLoginArray['token'] = $access_token['oauth_token'];

        $twId = $content->id;
        $socialLoginArray['id'] = $twId;
        if (isset($content->email)) {
            $twEmail = $content->email;
        } else {
            $twEmail = null;
        }
        $socialLoginArray['email'] = $twEmail;

        if (isset($content->name)) {
            $twName = $content->name;
        } else {
            $twName = null;
        }
        $socialLoginArray['userName'] = $twName;

        if (isset($content->screen_name)) {
            $twScreenName = $content->screen_name;
        } else {
            $twScreenName = null;
        }
        $socialLoginArray['networkUserName'] = $twScreenName;

        $userArray = array();

        $eventCode = $request->query->get("eventCode");
        $userEmail = $request->query->get("email");
        $userArray['eventCode'] = $eventCode;
        $userArray['email'] = $userEmail;

        $session = $request->getSession();
        $userSession = $session->get("userSession");


        if ($userSession)
            $this->socialAddInfo($request, $socialLoginArray);
        else
            $socialLogin = $this->socialLogin($request, $userArray, $socialLoginArray);

        $urlToRedirect = base64_decode($request->query->get("urlToRedirect"));

        return $this->redirect($urlToRedirect);

        /* $response = new Response();
          $response->setStatusCode(300);
          $response->setContent(json_encode($userArray));
          return $response; */
    }

    private function accountFbKitAction(Request $request) {
        $responseArray = array();
        $response = new Response();
        $userJson = $request->request->get('user');
        if ($userJson === null) {
            $response->setStatusCode(400);
            $response->setContent('{"error": null post data "}');
            return $response;
        }
        $userArray = json_decode($userJson, true);


        $options = array(
            CURLOPT_RETURNTRANSFER => true, // return web page
        );

        $ch = curl_init($this->container->getParameter('fb_account_kit_token_exchange_url') . '?grant_type=authorization_code&code=' . $userArray["code"] . '&access_token=AA|' . $this->container->getParameter('fb_client_id') . '|' . $this->container->getParameter('fb_account_kit_secret'));
        curl_setopt_array($ch, $options);
        $data = json_decode(curl_exec($ch), true);
        curl_close($ch);


        $user_id = $data['id'];
        $user_access_token = $data['access_token'];
        $refresh_interval = $data['token_refresh_interval_sec'];

        // Get Account Kit information
        $ch = curl_init($this->container->getParameter('fb_account_kit_me_url') . '?access_token=' . $user_access_token);
        curl_setopt_array($ch, $options);
        $data = json_decode(curl_exec($ch), true);
        curl_close($ch);

        $phone = isset($data['phone']) ? "+" . $data['phone']['country_prefix'] . "-" . $data['phone']['national_number'] : null;
        $email = isset($data['email']) ? $data['email']['address'] : null;

        $responseArray['id'] = $user_id;
        $responseArray['phone'] = $phone;
        $responseArray['email'] = $email;

        $response->setContent(json_encode($responseArray));

        return $response;
    }

    public function accountFbKitLoginAction(Request $request) {
        $responseArray = array();
        $response = new Response();

        $userJson = $request->request->get('user');
        if ($userJson === null) {
            $response->setStatusCode(400);
            $response->setContent('{"error": null post data "}');
            return $response;
        }
        $userArray = json_decode($userJson, true);


        $data = json_decode($this->accountFbKitAction($request)->getContent(), true);

        $socialLoginArray = array();
        $socialLoginArray['name'] = "afk"; //accountfbKit
        $socialLoginArray['id'] = $data['id'];
        if ($data['email'] !== null) {
            $email = $data['email'];
        } else {
            $email = null;
        }
        $socialLoginArray['email'] = $email;
        if ($data['phone'] !== null) {
            $phone = $data['phone'];
        } else {
            $phone = null;
        }
        $socialLoginArray['phone'] = $phone;


        $session = $request->getSession();
        $userSession = $session->get("userSession");
        if ($userSession) {
            return $this->socialAddInfo($request, $userArray, $socialLoginArray);
        } else {
            return $this->socialLogin($request, $userArray, $socialLoginArray);
        }
    }

    //user parameter is passed when you want to add new social network to an already existing user
    private function socialLogin(Request $request, $userArray, $socialLoginArray) {

        $session = $request->getSession();

        $response = new Response();
        $responseArray = array();

        $network = $this->getDoctrine()->getRepository('AppBundle:Network')->findOneBy(array("name" => $socialLoginArray['name'], "networkCode" => $socialLoginArray['id']));


        $em = $this->getDoctrine()->getManager();
        $event = null;
        $user = null;
        $userSocial = null;


        if (!$network) {

            if (isset($userArray['eventCode'])) {

                $event = $this->getDoctrine()->getRepository('AppBundle:Event')->find($userArray['eventCode']);
                if ($event) {
                    $user = $event->getReceiver();
                }
            }

            if (!$user && isset($userArray['email'])) {
                $userInfo = $this->getDoctrine()->getRepository('AppBundle:UserInfo')->findOneBy(array('type' => "email", "info" => $userArray['email']));
                if ($userInfo) {
                    $user = $userInfo->getUser();
                }
            }

            // find the userInfo by Email 
            if (isset($socialLoginArray['email']) && $socialLoginArray['email'] != null) {
                $userInfo = $this->getDoctrine()->getRepository('AppBundle:UserInfo')->findOneBy(array('type' => "email", "info" => $socialLoginArray['email']));
                if ($userInfo) {
                    $userSocial = $userInfo->getUser();
                    if ($user && $user->getCode() !== $userSocial->getCode()) {
                        $response->setStatusCode(300);
                        $responseArray["error"] = "El correo, " . $socialLoginArray['email'] . ", registrado con la red social, esta siendo utilizado por otro usuario.";

                        if ($socialLoginArray['name'] === "tw") {
                            $session->getFlashBag()->add('errorSocialLogin', $responseArray['error']);
                        } else {
                            $response->setContent(json_encode($responseArray));
                        }
                        return $response;
                    }
                }
            }

            if (!$userSocial) {
                if (isset($socialLoginArray['phone']) && $socialLoginArray['phone'] != null) {
                    $userInfo = $this->getDoctrine()->getRepository('AppBundle:UserInfo')->findOneBy(array('type' => "phone", "info" => $socialLoginArray['phone']));
                    if ($userInfo) {
                        $userSocial = $userInfo->getUser();
                        $response->setStatusCode(300);
                        $responseArray["error"] = "El telefono, " . $socialLoginArray['phone'] . ", registrado con la red social, esta siendo utilizado por otro usuario.";

                        if ($socialLoginArray['name'] === "tw") {
                            $session->getFlashBag()->add('errorSocialLogin', $responseArray['error']);
                        } else {
                            $response->setContent(json_encode($responseArray));
                        }

                        return $response;
                    }
                }
            }


            if (!$user && $userSocial) {
                $user = $userSocial;
            }


            if (!$user) {

                $user = new User();

                if (isset($socialLoginArray['email']) && $socialLoginArray['email'] !== null) {
                    $user->setEmail($socialLoginArray['email']);
                }
                if (isset($socialLoginArray['phone']) && $socialLoginArray['phone'] !== null) {
                    $user->setPhone($socialLoginArray['phone']);
                }
                if (isset($socialLoginArray['userName']) && $socialLoginArray['userName'] !== null) {
                    $user->setName($socialLoginArray['userName']);
                }
                $user->setActive(true);
                $em->persist($user);
                $em->flush();
            }

            $user->setActive(true);

            $em->persist($user);

            $network = new Network();
            $network->setName($socialLoginArray['name']);
            $network->setToken($socialLoginArray['token']);
            $network->setNetworkCode($socialLoginArray['id']);
            $network->setEmail($socialLoginArray['email']);
            if (isset($socialLoginArray['phone'])) {
                $network->setPhone($socialLoginArray['phone']);
            }
            $network->setUser($user);
            $network->setVisible(true);
            $em->persist($network);
            $em->flush();
        } else {

            //if a transfer its made to a new user, but it logins with social network
            // we add the email to the pre-existent user
            // and change the receiver of the promise for the pre-existant user-
            // delete from userInfo table, the data of the user we are gonna delete
            // delete the new user

            if (isset($userArray['eventCode'])) {

                if (!$event)
                    $event = $this->getDoctrine()->getRepository('AppBundle:Event')->find($userArray['eventCode']);

                if ($event->getAction() === "transfer") {
                    $userToTransfer = $event->getReceiver();

                    //It has to be an Inactive user, new one, created by the transfer action 
                    if (!$userToTransfer->getActive() && $userToTransfer->getCode() != $network->getUser()->getCode()) {

                        //update the event Receiver to the pre-existent user
                        $event->setReceiver($network->getUser());
                        $em->persist($event);

                        // keep the email from the deleting user
                        $newEmailForUserInfo = $userToTransfer->getEmail();
                        //remove the userInfo from non active user
                        $userInfo = $this->getDoctrine()->getRepository('AppBundle:UserInfo')->findOneBy(array("type" => "email", "info" => $userToTransfer->getEmail()));
                        $em->remove($userInfo);

                        //remove the non-active user
                        $em->remove($userToTransfer);
                        $em->flush();

                        //create  new info for the pre-existent user
                        $userInfo = new UserInfo();
                        $userInfo->setUser($network->getUser());
                        $userInfo->setType("email");
                        $userInfo->setInfo($newEmailForUserInfo);
                        $userInfo->setActive(true);
                        $userInfo->setVisible(true);
                        $em->persist($userInfo);

                        //update pre-existent user if new data is available
                        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($network->getUser()->getCode());
                        if (!$user->getEmail()) {
                            $user->setEmail($newEmailForUserInfo);
                        }
                        if (!$user->getName()) {
                            $user->setName($socialLoginArray['userName']);
                        }
                        $em->persist($user);
                        $em->flush();
                    }
                }
                //if there is no event. $userArray['eventCode'] 
            } elseif (isset($userArray['email'])) {
                $userInfo = $this->getDoctrine()->getRepository('AppBundle:UserInfo')->findOneBy(array('type' => "email", "info" => $userArray['email']));
                if ($userInfo) {
                    $user = $userInfo->getUser();
                }
            }
        }

        if (!$user) {
            $user = $network->getUser();
        }

        if ($user->getCode() !== $network->getUser()->getCode()) {
            $response->setStatusCode(300);
            $responseArray["error"] = "La red social, esta siendo utilizada por otro usuario.";

            if ($socialLoginArray['name'] === "tw") {
                $session->getFlashBag()->add('errorSocialLogin', $responseArray['error']);
            } else {

                $response->setContent(json_encode($responseArray));
            }
            return $response;
        }

        if (isset($socialLoginArray['userName']) && $socialLoginArray['userName'] !== null) {
            if (!$user->getName()) {
                $user->setName($socialLoginArray['userName']);
            }
        }
        //add email to user and userinfo
        if (isset($socialLoginArray['email']) && $socialLoginArray['email'] != null) {
            if (!$user->getEmail()) {
                $user->setEmail($socialLoginArray['email']);
            } else {
                $userInfo = $this->getDoctrine()->getRepository('AppBundle:UserInfo')->findOneBy(array("type" => 'email', "info" => $user->getEmail()));
                if ($userInfo && !$userInfo->getActive()) {
                    $userInfo->setActive(true);
                    $em->persist($userInfo);
                }
            }
            $userInfo = $this->getDoctrine()->getRepository('AppBundle:UserInfo')->findOneBy(array("type" => 'email', "info" => $socialLoginArray['email']));
            if (!$userInfo) {
                $userInfo = new UserInfo();
                $userInfo->setUser($user);
                $userInfo->setType("email");
                $userInfo->setInfo($socialLoginArray['email']);
                $userInfo->setActive(true);
                $userInfo->setVisible(true);
                $em->persist($userInfo);
            } else {
                if ($userInfo->getUser()->getCode() !== $user->getCode()) {
                    $response->setStatusCode(300);
                    $responseArray["error"] = "El correo, " . $socialLoginArray['email'] . ", registrado con la red social, esta siendo utilizado por otro usuario.";
                    $response->setContent(json_encode($responseArray));
                    return $response;
                }
            }
        }

        if (isset($socialLoginArray['phone']) && $socialLoginArray['phone'] != null) {
            if (!$user->getPhone()) {
                $user->setPhone($socialLoginArray['phone']);
            } else {
                $userInfo = $this->getDoctrine()->getRepository('AppBundle:UserInfo')->findOneBy(array("type" => 'phone', "info" => $user->getPhone()));
                if ($userInfo && !$userInfo->getActive()) {
                    $userInfo->setActive(true);
                    $em->persist($userInfo);
                }
            }
            $userInfo = $this->getDoctrine()->getRepository('AppBundle:UserInfo')->findOneBy(array("type" => 'phone', "info" => $socialLoginArray['phone']));
            if (!$userInfo) {
                $userInfo = new UserInfo();
                $userInfo->setUser($user);
                $userInfo->setType("phone");
                $userInfo->setInfo($socialLoginArray['phone']);
                $userInfo->setActive(true);
                $userInfo->setVisible(true);
                $em->persist($userInfo);
            } else {
                if ($userInfo->getUser()->getCode() !== $user->getCode()) {
                    $response->setStatusCode(300);
                    $responseArray["error"] = "El telefono, " . $socialLoginArray['phone'] . " que utiliza la red social, esta siendo utilizado por otro usuario.";
                    $response->setContent(json_encode($responseArray));
                    return $response;
                }
            }
        }

        if (isset($socialLoginArray['networkUserName']) && $socialLoginArray['networkUserName'] != null) {

            $network->setUserName($socialLoginArray['networkUserName']);
            $network->setUserNameVerified(true);
        }
        //update token
        $network->setToken($socialLoginArray['token']);
        $em->persist($network);
        $em->flush();



        $user->getDisplayName(); //?????? without this,or any other user method i dont get the other object properties, name, email
        $session->set("userSession", $user);

        $responseArray['success'] = $socialLoginArray['name'] . "Login";


        $response->setContent(json_encode($responseArray));
        return $response;
    }

    public function getInfoFbKitAction(Request $request) {
        $session = $request->getSession();
        $userSession = $session->get("userSession");
        if ($userSession) {
            $em = $this->getDoctrine()->getManager();
            $responseArray = array();
            $response = new Response();

            $userJson = $request->request->get('user');
            if ($userJson === null) {
                $response->setStatusCode(400);
                $response->setContent('{"error": null post data "}');
                return $response;
            }
            $userArray = json_decode($userJson, true);

            $data = json_decode($this->accountFbKitAction($request)->getContent(), true);
            if ($data['phone']) {
                $userInfo = $this->getDoctrine()->getRepository('AppBundle:UserInfo')->findOneBy(array('info' => $data['phone']));
                if ($userInfo) {
                    $response->setStatusCode(300);
                    $responseArray['phone'] = $data['phone'];
                    $responseArray['error'] = "El telefono " . $data['phone'] . "  esta siendo utilizado por otro usuario.";
                } else {
                    $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($userSession);

                    $userInfo = new UserInfo();
                    $userInfo->setUser($user);
                    $userInfo->setType("phone");
                    $userInfo->setInfo($data['phone']);
                    $userInfo->setActive(true);
                    $userInfo->setVisible(true);
                    $em->persist($userInfo);
                    if (!$user->getPhone()) {
                        $user->setPhone($data['phone']);
                        $em->persist($user);
                        $session->set("userSession", $user);
                    }

                    $em->flush();
                    $responseArray['phone'] = $data['phone'];
                    $responseArray['success'] = "El telefono " . $data['phone'] . " se ha agregado a tu usuario!";
                }
            }
            if ($data['email']) {
                $userInfo = $this->getDoctrine()->getRepository('AppBundle:UserInfo')->findOneBy(array('info' => $data['email']));
                if ($userInfo) {
                    $response->setStatusCode(300);
                    $responseArray['email'] = $data['email'];
                    $responseArray['error'] = "El correo " . $data['email'] . "  esta siendo utilizado por otro usuario.";
                } else {
                    $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($userSession);

                    $userInfo = new UserInfo();
                    $userInfo->setUser($user);
                    $userInfo->setType("email");
                    $userInfo->setInfo($data['email']);
                    $userInfo->setActive(true);
                    $userInfo->setVisible(true);
                    $em->persist($userInfo);
                    if (!$user->getEmail()) {
                        $user->setEmail($data['email']);
                        $em->persist($user);
                        $session->set("userSession", $user);
                    }

                    $em->flush();
                    $responseArray['email'] = $data['email'];
                    $responseArray['success'] = "El correo " . $data['email'] . " se ha agregado a tu usuario!";
                }
            }
        } else {
            $response->setStatusCode(300);
            $responseArray["error"] = "No hay session";
        }
        $response->setContent(json_encode($responseArray));
        return $response;
    }

    private function socialAddInfo(Request $request, $socialLoginArray) {
        $session = $request->getSession();
        $userSession = $session->get("userSession");
        $response = new Response();
        $responseArray = array();
        $em = $this->getDoctrine()->getManager();

        $network = $this->getDoctrine()->getRepository('AppBundle:Network')->findOneBy(array("name" => $socialLoginArray['name'], "networkCode" => $socialLoginArray['id']));
        if ($network) {
            if ($network->getUser()->getCode() !== $userSession->getCode()) {
                $response->setStatusCode(300);
                $responseArray['error'] = "La cuenta, de la red social que deseas agregar, esta siendo utilizada por otro usuario. ";
            } else {//????
                $response->setStatusCode(300);
                $responseArray['error'] = "Ya agregaste esta cuenta!. ";
            }

            if ($socialLoginArray['name'] === "tw")
                $session->getFlashBag()->add('errorAddSocialInfo', $responseArray['error']);

            $response->setContent(json_encode($responseArray));
            return $response;
        }

        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($userSession);

        $network = new Network();
        $network->setName($socialLoginArray['name']);
        $network->setToken($socialLoginArray['token']);
        $network->setNetworkCode($socialLoginArray['id']);
        $network->setVisible(true);
        $network->setUser($user);
        if (isset($socialLoginArray['userName']) && $socialLoginArray['userName'] !== null && $user->getName() === null) {
            $user->setName($socialLoginArray['userName']);
        }
        if (isset($socialLoginArray['email']) && $socialLoginArray['email'] !== null) {
            $userInfo = $this->getDoctrine()->getRepository('AppBundle:UserInfo')->findOneBy(array("type" => 'email', "info" => $socialLoginArray['email']));
            if (!$userInfo) {
                $userInfo = new UserInfo();
                $userInfo->setUser($user);
                $userInfo->setType("email");
                $userInfo->setInfo($socialLoginArray['email']);
                $userInfo->setActive(true);
                $userInfo->setVisible(true);
                $em->persist($userInfo);
            } else {
                if ($userInfo->getUser()->getCode() !== $user->getCode()) {
                    $response->setStatusCode(300);
                    $responseArray["error"] = "El correo, " . $socialLoginArray['email'] . " que utiliza la red social, esta siendo utilizado por otro usuario.";
                    $response->setContent(json_encode($responseArray));
                    return $response;
                }
            }

            if ($user->getEmail() == null) {
                $user->setEmail($socialLoginArray['email']);
            }
        }

        if (isset($socialLoginArray['networkUserName']) && $socialLoginArray['networkUserName'] != null) {

            $network->setUserName($socialLoginArray['networkUserName']);
            $network->setUserNameVerified(true);
        }

        $em->persist($network);
        $em->persist($user);
        $em->flush();
        $responseArray["success"] = "Se agrego con exito la red social a tu perfil! Espera unos segundos...";
        if ($socialLoginArray['name'] === "tw")
            $session->getFlashBag()->add('successAddSocialInfo', "Se agrego con exito la red social a tu perfil!");

        $response->setContent(json_encode($responseArray));

        $user->getDisplayName(); //?????? without this,or any other user method i dont get the other object properties, name, email
        $session->set("userSession", $user);
        return $response;
    }

}
