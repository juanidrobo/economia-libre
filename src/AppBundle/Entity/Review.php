<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="review")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\ReviewRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Review {

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
     * @ORM\Column(type="text", name="Review")
     */
    private $review;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="User", referencedColumnName="Code")
     */
    private $user;

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

    public function getReview() {
        return $this->review;
    }

    public function setReview($review) {
        $this->review = $review;
    }

    public function getUser() {
        return $this->user;
    }

    public function setUser($user) {
        $this->user = $user;
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
