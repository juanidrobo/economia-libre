<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="event")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\EventRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Event {

    /**
     * @ORM\Column(type="guid",name="Code")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $code;

    /**
     *   
     * @ORM\ManyToOne(targetEntity="Promise")
     * @ORM\JoinColumn(name="Promise", referencedColumnName="Code")
     */
    private $promise;

    /**
     * @ORM\Column(type="text",name="Action")
     */
    private $action;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="Owner", referencedColumnName="Code")
     */
    private $owner;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="Receiver", referencedColumnName="Code")
     */
    private $receiver;

    /**
     * @ORM\Column(type="datetime", name="Date")
     */
    private $date;

    public function getCode() {
        return $this->code;
    }

    public function setCode($code) {
        $this->code = $code;
    }

    public function getPromise() {
        return $this->promise;
    }

    public function setPromise($promise) {
        $this->promise = $promise;
    }

    public function getAction() {
        return $this->action;
    }

    public function setAction($action) {
        $this->action = $action;
    }

    public function getOwner() {
        return $this->owner;
    }

    public function setOwner($owner) {
        $this->owner = $owner;
    }

    public function getReceiver() {
        return $this->receiver;
    }

    public function setReceiver($receiver) {
        $this->receiver = $receiver;
    }

    public function getDate() {
        return $this->date;
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

}
