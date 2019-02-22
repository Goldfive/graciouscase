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
    private $entity;
    private $query;
    private $persist;

    public function __construct($entity, $query, $em, $persist = false)
    {
        $this->em = $em;
        $this->entity = $entity;
        $this->query = $query;
        $this->persist = $persist;
    }

    /**
     * @return array|null
     * @throws \Exception
     */
    public function handleData()
    {
        if (!isset($this->query['id']))
        {
            $this->query['id'] = '';
        }

        $urls = [
            'location'  => 'https://rickandmortyapi.com/api/location/'.$this->query['id'],
            'character' => 'https://rickandmortyapi.com/api/character/'.$this->query['id'],
            'episode'   => 'https://rickandmortyapi.com/api/episode/'.$this->query['id'],
        ];

        unset($this->query['id']);
        $return = [];
        $nextUrl = null;

        try {
            if (isset($urls[$this->entity])) {
                $url = $urls[$this->entity];
                do {
                    $objectData = json_decode($this->connect(($nextUrl ?: $url), $this->query)->raw_body, true);
                    $bool = substr($url, -1, 1);
                    //We're dealing with pagination
                    if (isset($objectData['info'])) {
                        $nextUrl = $objectData['info']['next'];
                    }

                    $return[] = $this->saveEntitiesToDatabase($this->entity, $objectData);
                } while ($nextUrl !== ""  && !is_numeric(substr($url, -1, 1)));
            } else if ($this->entity == 'all') {
                foreach ($urls as $entity => $url) {
                    do {
                        $objectData = json_decode($this->connect(($nextUrl ?: $url), $this->query)->raw_body, true);

                        //We're dealing with pagination
                        if (isset($objectData['info'])) {
                            $nextUrl = $objectData['info']['next'];
                            if ($nextUrl == "") {
                                $nextUrl = null;
                            }
                        }

                        $return[] = $this->saveEntitiesToDatabase($entity, $objectData);
                    } while ($nextUrl !== null && !is_numeric(substr($url, -1, 1)));
                }
            }
            return $return;
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        return null;
    }

    /**
     * @return \Unirest\Response
     * @throws Exception
     * @params $url
     * @params $query
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

    /**
     * @param string $entity
     * @param array $objectData
     * @return \App\Entity\Character|\App\Entity\Episode|\App\Entity\Location|array|null
     * @throws \Exception
     */
    public function saveEntitiesToDatabase(string $entity, array $objectData)
    {
        $isSingleRecord = !isset($objectData['info']);
        $info = ($isSingleRecord ? $objectData : $objectData['info']);
        $return = [];
        if (!$isSingleRecord) {
            foreach ($objectData['results'] as $key => $result) {
               $return[] = $this->save($entity, $result);
            }
            return $return;
        } else {
            return $this->save($entity, $info);
        }
    }

    /**
     * @param string $entity
     * @param array $result
     * @return \App\Entity\Character|\App\Entity\Episode|\App\Entity\Location|null
     * @throws \Exception
     */
    private function save(string $entity, array $result)
    {
        $transformer = new ApiTransformer($entity, $result, $this->em);
        $entityObject = $transformer->transformToEntity();

        if ($entityObject !== null && $this->persist) {
            $this->em->persist($entityObject);
            $this->em->flush();
        }

        return $entityObject;
    }
}