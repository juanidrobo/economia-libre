<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Table(name="user",indexes={@ORM\Index(name="Email", columns={"email"}),@ORM\Index(name="Phone", columns={"phone"})})
 * @ORM\Entity(repositoryClass="AppBundle\Entity\UserRepository")
 * @ORM\HasLifecycleCallbacks
 */
class User {

    /**
     * @ORM\Column(type="guid", name="Code")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $code;

    /**
     * @ORM\Column(type="string", name="Email",nullable=true, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", name="Name", nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", name="Phone", nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="boolean", name="Active")
     */
    private $active;

    /**
     * @ORM\Column(type="string", name="Seckey", nullable=true)
     */
    private $seckey;

    /**
     * @ORM\Column(type="guid", name="Verification", nullable=true)
     */
    private $verification;

    /**
     * @ORM\Column(type="datetime", name="Date")
     */
    private $date;

    public function getCode() {
        return $this->code;
    }

    public function getName() {
        return $this->name;
    }

    public function getPhone() {
        return $this->phone;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getActive() {
        return $this->active;
    }

    public function getSecKey() {
        return $this->seckey;
    }

    public function getVerification() {
        return $this->verification;
    }

    public function getDate() {
        return $this->date;
    }

    public function setCode($code) {
        $this->name = $code;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setPhone($phone) {
        $this->phone = $phone;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function setActive($active) {
        $this->active = $active;
    }

    public function setSecKey($seckey) {
        $this->seckey = $seckey;
    }

    public function setVerification($verification) {
        $this->verification = $verification;
    }

    public function setDate($date) {
        $this->date = $date;
    }

    /**
     * @ORM\PrePersist
     */
    public function beforePersist() {
        $this->date = new \DateTime();
    }

    public function getDisplayName() {
        if ($this->name)
            return $this->name;
        else if ($this->email)
            return $this->email;
        else if ($this->phone)
            return $this->phone;
    }
        public function getOneIdentifier() {
        if ($this->email)
            return $this->email;
        else if ($this->phone)
            return $this->phone;
        else if ($this->code)
            return $this->code;
    }

}
