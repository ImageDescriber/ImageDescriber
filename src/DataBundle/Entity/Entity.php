<?php

namespace DataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;

/**
 * Entity
 *
 * @ORM\Table(name="entity")
 * @ORM\Entity(repositoryClass="DataBundle\Repository\EntityRepository")
 */
class Entity
{
    /**
     * @Serializer\Since("1.0")
     *
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Serializer\Since("1.0")
     *
     * @var int
     * @Assert\NotBlank()
     *
     * @ORM\Column(name="qwd", type="integer", unique=true)
     */
    private $qwd;

    /**
     * @Serializer\Since("1.0")
     *
     * @var array
     *
     * @ORM\Column(name="listDepicts", type="array", nullable=true)
     */
    private $listDepicts;

    /**
     * @Serializer\Since("1.0")
     *
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="createDate", type="datetime", nullable=false)
     */
    protected $createDate;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set qwd
     *
     * @param integer $qwd
     *
     * @return Entity
     */
    public function setQwd($qwd)
    {
        $this->qwd = $qwd;

        return $this;
    }

    /**
     * Get qwd
     *
     * @return integer
     */
    public function getQwd()
    {
        return $this->qwd;
    }

    /**
     * Set listDepicts
     *
     * @param array $listDepicts
     *
     * @return Entity
     */
    public function setListDepicts($listDepicts)
    {
        $this->listDepicts = $listDepicts;

        return $this;
    }

    /**
     * Get listDepicts
     *
     * @return array
     */
    public function getListDepicts()
    {
        return $this->listDepicts;
    }

    /**
     * Set createDate
     *
     * @param \DateTime $createDate
     *
     * @return Entity
     */
    public function setCreateDate($createDate)
    {
        $this->createDate = $createDate;

        return $this;
    }

    /**
     * Get createDate
     *
     * @return \DateTime
     */
    public function getCreateDate()
    {
        return $this->createDate;
    }
}
