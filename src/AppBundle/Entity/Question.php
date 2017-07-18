<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="question")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\QuestionRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Question {

    /**
     * @ORM\Column(type="guid",name="Code")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $code;

    /**
     *   
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="User", referencedColumnName="Code", nullable=true)
     */
    private $user;

    /**
     * @ORM\Column(type="text", name="Question")
     */
    private $question;

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

    public function getQuestion() {
        return $this->question;
    }

    public function setQuestion($question) {
        $this->question = $question;
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
