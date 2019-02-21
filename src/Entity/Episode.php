<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EpisodeRepository")
 */
class Episode
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
    private $air_date;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $episodeCode;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="App\Entity\Character", inversedBy="episodes")
     */
    private $characters;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $url;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * Episode constructor.
     * @param $name
     * @param $air_date
     * @param $episodeCode
     * @param $characters
     * @param $url
     * @param $created
     * @throws \Exception
     */
    public function __construct($name, $air_date, $episodeCode, $characters, $url, $created)
    {
        $this->characters = new ArrayCollection();
        $this->name = $name;
        $this->air_date = $air_date;
        $this->episodeCode = $episodeCode;
        $this->characters = $characters;
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

    public function getAirDate(): ?\DateTimeInterface
    {
        return $this->air_date;
    }

    public function setAirDate(\DateTimeInterface $air_date): self
    {
        $this->air_date = $air_date;

        return $this;
    }

    public function getEpisodeCode(): ?string
    {
        return $this->episodeCode;
    }

    public function setEpisodeCode(string $episodeCode): self
    {
        $this->episodeCode = $episodeCode;

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
        }

        return $this;
    }

    public function removeCharacter(Character $character): self
    {
        if ($this->characters->contains($character)) {
            $this->characters->removeElement($character);
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
}
