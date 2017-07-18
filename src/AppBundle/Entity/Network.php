<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="network",indexes={@ORM\Index(name="network_code", columns={"NetworkCode"})},
 * uniqueConstraints={@ORM\UniqueConstraint(name="UniqueNetwrok", columns={"Name", "NetworkCode"})})
 * @ORM\Entity(repositoryClass="AppBundle\Entity\NetworkRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Network {

    /**
     * @ORM\Column(type="guid",name="Code")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $code;

    /**
     * @ORM\Column(type="string",name="Name")
     */
    private $name;

    /**
     * @ORM\Column(type="string", name="NetworkCode")
     */
    private $networkCode;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="User", referencedColumnName="Code")
     */
    private $user;

    /**
     * @ORM\Column(type="string", name="Email", nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", name="Phone", nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", name="UserName", nullable=true)
     */
    private $userName;

    /**
     * @ORM\Column(type="boolean", name="UserNameVerified", nullable=true)
     */
    private $userNameVerified;

    /**
     * @ORM\Column(type="datetime", name="Date")
     */
    private $date;

    /**
     * @ORM\Column(type="boolean", name="Visible")
     */
    private $visible;

    /**
     * @ORM\Column(type="string", name="Token", nullable=true)
     */
    private $token;

    public function getCode() {
        return $this->code;
    }

    public function setCode($code) {
        $this->code = $code;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getUser() {
        return $this->user;
    }

    public function setUser($user) {
        $this->user = $user;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function getPhone() {
        return $this->phone;
    }

    public function setPhone($phone) {
        $this->phone = $phone;
    }

    public function getNetworkCode() {
        return $this->networkCode;
    }

    public function setNetworkCode($networkCode) {
        $this->networkCode = $networkCode;
    }

    public function getDate() {
        return $this->date;
    }

    public function setDate($date) {
        $this->date = $date;
    }

    public function getVisible() {
        return $this->visible;
    }

    public function setVisible($visible) {
        $this->visible = $visible;
    }

    public function getToken() {
        return $this->token;
    }

    public function setToken($token) {
        $this->token = $token;
    }

    public function getUserName() {
        return $this->userName;
    }

    public function setUserName($userName) {
        $this->userName = $userName;
    }

    public function getUserNameVerified() {
        return $this->userNameVerified;
    }

    public function setUserNameVerified($userNameVerified) {
        $this->userNameVerified = $userNameVerified;
    }

    public function getDisplayName() {
        if ($this->name === "fb") {
            return "Facebook.com";
        }
        if ($this->name === "g") {
            return "Google.com";
        }
        if ($this->name === "tw") {
            return "Twitter.com";
        }
    }

    public function getDisplayUrl() {
        if ($this->name === "fb") {
            return "https://www.facebook.com";
        }
        if ($this->name === "g") {
            return "https://plus.google.com";
        }
        if ($this->name === "tw") {
            return "https://www.twitter.com";
        }
    }

    /**
     * @ORM\PrePersist
     */
    public function beforePersist() {
        $this->date = new \DateTime();
    }

}
