<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CharacterRepository")
 * @ORM\Table(name="characters")
 */
class Character
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
    private $status;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $species;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $gender;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Location", inversedBy="residents")
     * @ORM\JoinColumn(nullable=true)
     */
    private $location = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Location", inversedBy="characters")
     * @ORM\JoinColumn(nullable=true)
     */
    private $origin = null;

    /**
     * @var Episode
     * @ORM\ManyToMany(targetEntity="App\Entity\Episode", mappedBy="characters")
     */
    private $episodes;

    /**
     * Character constructor.
     * @param $id
     * @param $name
     * @param $status
     * @param $species
     * @param $type
     * @param $gender
     * @param $image
     * @param $url
     * @param $created
     * @param $location
     * @param $origin
     * @param $episodes
     * @throws \Exception
     */
    public function __construct($id, $name, $status, $species, $type, $gender, $image, $url, $created, $location, $origin, $episodes)
    {
        $this->episodes = new ArrayCollection();
        $this->id = $id;
        $this->name = $name;
        $this->status = $status;
        $this->species = $species;
        $this->type = $type;
        $this->gender = $gender;
        $this->image = $image;
        $this->url = $url;
        $date = new \DateTime($created);
        $this->created = $date;
        $this->location = $location;
        $this->origin = $origin;
        foreach ($episodes as $episode) {
            $this->addEpisode($episode);
        }
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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getSpecies(): ?string
    {
        return $this->species;
    }

    public function setSpecies(string $species): self
    {
        $this->species = $species;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
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

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(?Location $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getOrigin(): ?Location
    {
        return $this->origin;
    }

    public function setOrigin(?Location $origin): self
    {
        $this->origin = $origin;

        return $this;
    }

    /**
     * @return Collection|Episode[]
     */
    public function getEpisodes(): Collection
    {
        return $this->episodes;
    }

    public function addEpisode(Episode $episode): self
    {
        if (!$this->episodes->contains($episode)) {
            $this->episodes[] = $episode;
            $episode->addCharacter($this);
        }

        return $this;
    }

    public function getLastEpisode()
    {
        return $this->episodes->last();
    }

    public function removeEpisode(Episode $episode): self
    {
        if ($this->episodes->contains($episode)) {
            $this->episodes->removeElement($episode);
            $episode->removeCharacter($this);
        }

        return $this;
    }
}
