<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\ProfileRepository")
 */
class Profile {

    /**
     * @ORM\Column(type="guid")
     * @ORM\Id
     */
    private $code;

    

}
