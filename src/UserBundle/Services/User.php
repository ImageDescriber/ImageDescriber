<?php

namespace UserBundle\Services;

use AppBundle\Entity\Entity;
use AppBundle\Entity\Resource;
use AppBundle\Entity\Transcript;
use AppBundle\Repository\ResourceRepository;
use AppBundle\Services\Versioning;
use Doctrine\ORM\EntityManager;
use UserBundle\Entity\Preference;

class User
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }
}