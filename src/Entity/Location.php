<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LocationRepository")
 */
class Location
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $dimension;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Character", mappedBy="location")
     */
    private $residents;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $url;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Character", mappedBy="origin")
     */
    private $characters;

    /**
     * Location constructor.
     * @param $name
     * @param $type
     * @param $dimension
     * @param $url
     * @param $created
     * @throws \Exception
     */
    public function __construct($name, $type, $dimension, $url, $created)
    {
        $this->residents = new ArrayCollection();
        $this->characters = new ArrayCollection();
        $this->name = $name;
        $this->type = $type;
        $this->dimension = $dimension;
        $this->url = $url;
        $this->created = new \DateTime($created);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getDimension(): ?string
    {
        return $this->dimension;
    }

    public function setDimension(string $dimension): self
    {
        $this->dimension = $dimension;

        return $this;
    }

    /**
     * @return Collection|Character[]
     */
    public function getResidents(): Collection
    {
        return $this->residents;
    }

    public function addResident(Character $resident): self
    {
        if (!$this->residents->contains($resident)) {
            $this->residents[] = $resident;
            $resident->setLocation($this);
        }

        return $this;
    }

    public function removeResident(Character $resident): self
    {
        if ($this->residents->contains($resident)) {
            $this->residents->removeElement($resident);
            // set the owning side to null (unless already changed)
            if ($resident->getLocation() === $this) {
                $resident->setLocation(null);
            }
        }

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(\DateTimeInterface $created): self
    {
        $this->created = $created;

        return $this;
    }

    /**
     * @return Collection|Character[]
     */
    public function getCharacters(): Collection
    {
        return $this->characters;
    }

    public function addCharacter(Character $character): self
    {
        if (!$this->characters->contains($character)) {
            $this->characters[] = $character;
            $character->setOrigin($this);
        }

        return $this;
    }

    public function removeCharacter(Character $character): self
    {
        if ($this->characters->contains($character)) {
            $this->characters->removeElement($character);
            // set the owning side to null (unless already changed)
            if ($character->getOrigin() === $this) {
                $character->setOrigin(null);
            }
        }

        return $this;
    }
}
