<?php

namespace App\Api;

use App\Entity\Character;
use App\Entity\Location;
use App\Entity\Episode;
use App\Repository\CharacterRepository;
use App\Repository\LocationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;

class ApiTransformer
{
    private $entity;
    private $em;
    private $locationRepo;
    private $characterRepo;
    private $episodeRepo;
    private $transformData;

    /**
     * ApiTransformer constructor.
     * @param string $entity
     * @param $transformData
     * @param EntityManagerInterface $em
     * @return Character|Location|Episode|null
     */
    public function __construct(string $entity, array $transformData, EntityManagerInterface $em)
    {
        $this->entity = $entity;
        $this->transformData = $transformData;
        $this->em = $em;

        $this->locationRepo = $this->em->getRepository(Location::class);
        $this->characterRepo = $this->em->getRepository(Character::class);
        $this->episodeRepo = $this->em->getRepository(Episode::class);
    }

    /**
     * @return Character|Location|Episode|null
     * @throws \Exception
     */
    public function transformToEntity()
    {
        $transformData = $this->transformData;
        $object = null;
        switch ($this->entity) {
            case 'character':
                $location = $this->locationRepo->findBy(['name' => $transformData['location']['name']]);
                $location = (sizeof($location) > 0 ? $location[0] : null);
                $origin = $this->locationRepo->findBy(['name' => $transformData['origin']['name']]);
                $origin = (sizeof($origin) > 0 ? $origin[0] : null);

                $episodes = new ArrayCollection();
                foreach ($transformData['episode'] as $episodeId => $url) {
                    $episode = $this->characterRepo->find($episodeId);
                    if ($episode instanceof Episode) {
                        $episode->add($episode);
                    }
                }

                if (($character = $this->characterRepo->find($transformData['id'])) === null) {
                    $object = new Character($transformData['id'], $transformData['name'], $transformData['status'], $transformData['species'], $transformData['type'], $transformData['gender'], $transformData['image'], $transformData['url'], $transformData['created'], $location, $origin, $episodes);
                 }
                break;
            case 'location':
                $object = new Location($transformData['name'], $transformData['type'], $transformData['dimension'], $transformData['url'], $transformData['created']);
                break;
            case 'episode':
                $characters = new ArrayCollection();
                foreach ($transformData['characters'] as $characterId => $url) {
                    $character = $this->characterRepo->find($characterId);
                    if ($character instanceof Character) {
                        $characters->add($character);
                    }
                }
                $object = new Episode($transformData['name'], $transformData['air_date'], $transformData['episode'], $characters, $transformData['url'], $transformData['created']);
                break;
        }

        return $object;
    }
}