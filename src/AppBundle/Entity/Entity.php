<?php

namespace AppBundle\Entity;

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
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EntityRepository")
 *
 * @Serializer\ExclusionPolicy("all")
 *
 * @UniqueEntity("qwd")
 * @UniqueEntity("image")
 *
 */
class Entity
{
    /**
     * @Serializer\Since("1.0")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "id", "entity"})
     *
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * The Wikidata number of the entity
     *
     * @Serializer\Since("1.0")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "id", "entity"})
     *
     * @var int
     * @Assert\NotBlank()
     *
     * @ORM\Column(name="qwd", type="integer", unique=true)
     */
    private $qwd;

    /**
     * The URL of the artwork's image
     *
     * @Serializer\Since("1.0")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content", "entity"})
     *
     * @var string
     * @Assert\NotBlank()
     * @Assert\Url()
     *
     * @ORM\Column(name="image", type="text", nullable=false)
     */
    private $image;

    /**
     * The Labels of the entity related to the depict
     *
     * @Serializer\Since("1.0")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content", "entity"})
     *
     * @var array
     *
     * @ORM\Column(name="labels", type="array", nullable=true)
     */
    private $labels;

    /**
     * Keywords are used to find collections or artwork
     *
     * @Serializer\Since("1.0")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content", "entity"})
     *
     * @var array
     *
     * @ORM\Column(name="keywords", type="array", nullable=true)
     */
    private $keywords;

    /**
     * The status of submission
     *
     * @Serializer\Since("1.0")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content", "entity"})
     *
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255, nullable=true)
     */
    private $status;

    /**
     * @Serializer\Since("1.0")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "metadata", "entity"})
     *
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="createDate", type="datetime", nullable=false)
     */
    protected $createDate;

    /**
     * @Serializer\Since("1.0")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "depicts", "entity"})
     *
     * @ORM\OneToMany(targetEntity="Depict", mappedBy="entity", cascade={"persist", "remove"})
     */
    private $depicts;

    /**
     * @Serializer\Since("1.0")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "logs", "entity"})
     *
     * @ORM\OneToMany(targetEntity="Log", mappedBy="entity", cascade={"persist", "remove"})
     */
    private $logs;


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
     * Constructor
     */
    public function __construct()
    {
        $this->depicts = new \Doctrine\Common\Collections\ArrayCollection();
        $this->logs = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set labels
     *
     * @param array $labels
     *
     * @return Entity
     */
    public function setLabels($labels)
    {
        $this->labels = $labels;

        return $this;
    }

    /**
     * Get labels
     *
     * @return array
     */
    public function getLabels()
    {
        return $this->labels;
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

    /**
     * Set keywords
     *
     * @param array $keywords
     *
     * @return Entity
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;

        return $this;
    }

    /**
     * Get keywords
     *
     * @return array
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return Entity
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
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
     * Add depict
     *
     * @param \AppBundle\Entity\Depict $depict
     *
     * @return Entity
     */
    public function addDepict(\AppBundle\Entity\Depict $depict)
    {
        $this->depicts[] = $depict;

        return $this;
    }

    /**
     * Remove depict
     *
     * @param \AppBundle\Entity\Depict $depict
     */
    public function removeDepict(\AppBundle\Entity\Depict $depict)
    {
        $this->depicts->removeElement($depict);
    }

    /**
     * Get depicts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDepicts()
    {
        return $this->depicts;
    }

    /**
     * Add log
     *
     * @param \AppBundle\Entity\Log $log
     *
     * @return Entity
     */
    public function addLog(\AppBundle\Entity\Log $log)
    {
        $this->logs[] = $log;

        return $this;
    }

    /**
     * Remove log
     *
     * @param \AppBundle\Entity\Log $log
     */
    public function removeLog(\AppBundle\Entity\Log $log)
    {
        $this->logs->removeElement($log);
    }

    /**
     * Get logs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLogs()
    {
        return $this->logs;
    }
}
