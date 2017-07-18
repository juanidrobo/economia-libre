<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="userinfo",indexes={@ORM\Index(name="Info", columns={"info", "type"}),@ORM\Index(name="User", columns={"user"})},
 *   uniqueConstraints={@ORM\UniqueConstraint(name="UniqueInfo", columns={"type", "info"})})
 * @ORM\Entity(repositoryClass="AppBundle\Entity\UserInfoRepository")
 * @ORM\HasLifecycleCallbacks
 */
class UserInfo {

    /**
     * @ORM\Column(type="guid", name="Code")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $code;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="User", referencedColumnName="Code")
     */
    private $user;

    /**
     * @ORM\Column(type="string", name="Type")
     */
    private $type;

    /**
     * @ORM\Column(type="string", name="Info")
     */
    private $info;

    /**
     * @ORM\Column(type="boolean", name="Active")
     */
    private $active;

    /**
     * @ORM\Column(type="boolean", name="Visible")
     */
    private $visible;

    /**
     * @ORM\Column(type="datetime", name="Date")
     */
    private $date;

    public function getCode() {
        return $this->code;
    }

    public function getUser() {
        return $this->user;
    }

    public function getType() {
        return $this->type;
    }

    public function getInfo() {
        return $this->info;
    }

    public function getActive() {
        return $this->active;
    }
    public function getVisible() {
        return $this->visible;
    }

    public function getDate() {
        return $this->date;
    }

    public function setCode($code) {
        $this->code = $code;
    }

    public function setUser($user) {
        $this->user = $user;
    }

    public function setType($type) {
        $this->type = $type;
    }

    public function setInfo($info) {
        $this->info = $info;
    }

    public function setActive($active) {
        $this->active = $active;
    }

    public function setVisible($visible) {
        $this->visible = $visible;
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
