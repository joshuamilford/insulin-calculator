<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="entry")
 */
class Entry
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="float")
     * @Assert\NotBlank(message="This is required")
     */
    private $bgl;

    /**
     * @ORM\Column(type="float")
     */
    private $carbs = 0;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Assert\NotBlank(groups={"calculate"}, message="This is required")
     */
    private $ratio;

    /**
     * @ORM\Column(type="float")
     */
    private $calculatedUnits = 0;

    /**
     * @ORM\Column(type="float")
     * @Assert\NotBlank(groups={"save"})
     */
    private $actualUnits = 0;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank()
     * @Assert\DateTime()
     */
    private $createdAt;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="entries")
     */
    private $user;

    /**
     * @ORM\Column(type="string")
     */
    private $correctionUnits = 0;

    /**
     * @ORM\Column(type="string")
     */
    private $correctionThreshold = 0;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $notes;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\EntryFood", mappedBy="entry", cascade={"persist"})
     */
    private $entryFoods;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Tag", inversedBy="entries")
     */
    private $tags;

    public function __construct()
    {
        $this->entryFoods = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->tags = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getBgl()
    {
        return $this->bgl;
    }

    /**
     * @param mixed $bgl
     */
    public function setBgl($bgl)
    {
        $this->bgl = $bgl;
    }

    /**
     * @return mixed
     */
    public function getCarbs()
    {
        return $this->carbs;
    }

    /**
     * @param mixed $carbs
     */
    public function setCarbs($carbs)
    {
        $this->carbs = $carbs;
    }

    /**
     * @return mixed
     */
    public function getRatio()
    {
        return $this->ratio;
    }

    /**
     * @param mixed $ratio
     */
    public function setRatio($ratio)
    {
        $this->ratio = $ratio;
    }

    /**
     * @return mixed
     */
    public function getCalculatedUnits()
    {
        return $this->calculatedUnits;
    }

    /**
     * @param mixed $calculatedUnits
     */
    public function setCalculatedUnits($calculatedUnits)
    {
        $this->calculatedUnits = $calculatedUnits;
    }

    /**
     * @return mixed
     */
    public function getActualUnits()
    {
        return $this->actualUnits;
    }

    /**
     * @param mixed $actualUnits
     */
    public function setActualUnits($actualUnits)
    {
        $this->actualUnits = $actualUnits;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getCorrectionUnits()
    {
        return $this->correctionUnits;
    }

    /**
     * @param mixed $correctionUnits
     */
    public function setCorrectionUnits($correctionUnits)
    {
        $this->correctionUnits = $correctionUnits;
    }

    /**
     * @return mixed
     */
    public function getCorrectionThreshold()
    {
        return $this->correctionThreshold;
    }

    /**
     * @param mixed $correctionThreshold
     */
    public function setCorrectionThreshold($correctionThreshold)
    {
        $this->correctionThreshold = $correctionThreshold;
    }

    public function setUnits()
    {
        if (!$this->ratio) {
            return;
        }
        $this->carbs = 0;
        foreach ($this->entryFoods as $food) {
            $this->carbs += ($food->getCarbs() * $food->getServings());
        }
        $correctionFactors = $this->user->getCorrectionFactors();
        $preCalculated = $this->carbs / $this->ratio;
        $correction = false;

        foreach ($correctionFactors as $factor) {
            if ($this->bgl >= $factor->getThreshold()) {
                $correction = $factor;
                $calculated = $preCalculated + $correction->getUnits();
            }
        }

        if ($correction) {
            $this->setCorrectionUnits($correction->getUnits());
            $this->setCorrectionThreshold($correction->getThreshold());
        } else {
            $calculated = $preCalculated;
        }

        $this->setCalculatedUnits($calculated);

    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param mixed $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return mixed
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @param mixed $notes
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;
    }

    /**
     * @return mixed
     */
    public function getEntryFoods()
    {
        return $this->entryFoods;
    }

    public function addEntryFood(EntryFood $entryFood)
    {
        $entryFood->setEntry($this);
        $this->entryFoods->add($entryFood);
    }

    public function removeEntryFood(EntryFood $entryFood)
    {
        $this->entryFoods->removeElement($entryFood);
    }

    /**
     * @return ArrayCollection|Tag[]
     */
    public function getTags()
    {
        return $this->tags;
    }

//    public function addTag(Tag $tag)
//    {
//        $this->tags[] = $tag;
//    }
}