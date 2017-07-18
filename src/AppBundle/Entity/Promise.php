<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="promise")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\PromiseRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Promise {

    /**
     * @ORM\Column(type="guid", name="Code")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $code;

    /**
     * @ORM\Column(type="text", name="Description")
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="Responsible", referencedColumnName="Code")
     */
    private $responsible;

    /**
     * @ORM\Column(type="boolean", name="Active")
     */
    private $active;

      /**
     * @ORM\Column(type="boolean", name="Visible")
     */
    private $visible;
    
    /**
     * @ORM\Column(type="string", length=50, name="Pubkey", nullable=true)
     */
    private $pubkey;

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

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function getResponsible() {
        return $this->responsible;
    }

    public function setResponsible($responsible) {
        $this->responsible = $responsible;
    }

    public function getActive() {
        return $this->active;
    }

    public function setActive($active) {
        $this->active = $active;
    }

    
    public function getVisible() {
        return $this->visible;
    }

    public function setVisible($visible) {
        $this->visible = $visible;
    }
    
    public function getPubKey() {
        return $this->pubkey;
    }

    public function setPubKey($pubkey) {
        $this->pubkey = $pubkey;
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
