<?php

namespace DataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Entity
 *
 * @ORM\Table(name="entity")
 * @ORM\Entity(repositoryClass="DataBundle\Repository\EntityRepository")
 *
 * @Serializer\ExclusionPolicy("all")
 *
 * @UniqueEntity("qwd")
 * @UniqueEntity("image")
 *
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "get_entity",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      )
 * )
 * @Hateoas\Relation(
 *      "modify",
 *      href = @Hateoas\Route(
 *          "update_entity",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      )
 * )
 * @Hateoas\Relation(
 *      "patch",
 *      href = @Hateoas\Route(
 *          "patch_entity",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      )
 * )
 * @Hateoas\Relation(
 *      "delete",
 *      href = @Hateoas\Route(
 *          "remove_entity",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      )
 * )
 */
class Entity
{
    /**
     * @Serializer\Since("1.0")
     * @Serializer\Expose
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
     * @Serializer\Expose
     *
     * @var int
     * @Assert\NotBlank()
     *
     * @ORM\Column(name="qwd", type="integer", unique=true)
     */
    private $qwd;

    /**
     * @Serializer\Since("1.0")
     * @Serializer\Expose
     *
     * @var string
     * @Assert\NotBlank()
     * @Assert\Url()
     *
     * @ORM\Column(name="image", type="string", unique=true)
     */
    private $image;

    /**
     * @Serializer\Since("1.0")
     * @Serializer\Expose
     *
     * @var array
     *
     * @ORM\Column(name="listDepicts", type="array", nullable=true)
     */
    private $listDepicts;

    /**
     * @Serializer\Since("1.0")
     * @Serializer\Expose
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

    /**
     * Set image
     *
     * @param string $image
     *
     * @return Entity
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }
}
