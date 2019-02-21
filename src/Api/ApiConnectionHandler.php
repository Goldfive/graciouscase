<?php

namespace App\Api;

use Doctrine\ORM\EntityManagerInterface;
use Unirest\Exception;
use Unirest\Request;


class ApiConnectionHandler
{

    private static $perPage = 20;
    /** @var EntityManagerInterface*/
    private $em;

    public function __construct($entity, $query, $em)
    {
        $this->em = $em;

        if (!isset($query['id']))
        {
            $query['id'] = '';
        }

        $urls = [
            'location'  => 'https://rickandmortyapi.com/api/location/'.$query['id'],
            'character' => 'https://rickandmortyapi.com/api/character/'.$query['id'],
            'episode'   => 'https://rickandmortyapi.com/api/episode/'.$query['id'],
        ];

        unset($query['id']);

        try {
            if (isset($urls[$entity])) {
                $url = $urls[$entity];
                $objectData = json_decode($this->connect($url, $query)->raw_body, true);
                $this->saveEntitiesToDatabase($entity, $objectData);
            } else if ($entity == 'all') {
                foreach ($urls as $entity => $url) {
                    $objectData = json_decode($this->connect($url, $query)->raw_body, true);
                    $this->saveEntitiesToDatabase($entity, $objectData);
                }
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }

    }

    /**
     * @return \Unirest\Response
     * @throws Exception
     */
    public function connect($url, $query)
    {
        $headers = array('Accept' => 'application/json');

        $response = Request::get($url, $headers, $query);

        if ($response->code !== 200) {
            throw new Exception('Something went wrong! Response from host: '.$response->code.': '.$response->raw_body);
        }

        return $response;
    }

    public function saveEntitiesToDatabase(string $entity, array $objectData)
    {
        $isSingleRecord = !isset($objectData['info']);
        $info = ($isSingleRecord ? $objectData : $objectData['info']);
        if (!$isSingleRecord) {
            foreach ($objectData['results'] as $key => $result) {
               $this->save($entity, $result);
            }
        } else {
            $this->save($entity, $info);
        }
    }

    private function save(string $entity, array $result)
    {
        $transformer = new ApiTransformer($entity, $result, $this->em);
        $entityObject = $transformer->transformToEntity();

        if ($entityObject !== null) {
            $this->em->persist($entityObject);
            $this->em->flush();
        }
    }
}