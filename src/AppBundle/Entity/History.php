<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\HistoryRepository")
 */
class History {

    /**
     * @ORM\Column(type="guid")
     * @ORM\Id
     */
    private $code;

    

}
