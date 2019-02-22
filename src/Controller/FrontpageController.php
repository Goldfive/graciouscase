<?php

namespace App\Controller;

use App\Api\ApiConnectionHandler;
use App\Command\ImportToDatabaseFromAPICommand;
use App\Repository\CharacterRepository;
use App\Repository\EpisodeRepository;
use App\Repository\LocationRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Routing\Annotation\Route;

class FrontpageController extends AbstractController
{
    private $episodeRepository;
    private $locationRepository;
    private $characterRepository;
    private $em;

    private $connectionType;

    public function __construct(EpisodeRepository $episodeRepository, LocationRepository $locationRepository, CharacterRepository $characterRepository, EntityManagerInterface $em)
    {
        $this->episodeRepository = $episodeRepository;
        $this->locationRepository = $locationRepository;
        $this->characterRepository = $characterRepository;
        $this->em = $em;

        session_start();

        $this->connectionType = (isset($_SESSION['database_connection']) && $_SESSION['database_connection'] ? 'database' : 'api');

        if (sizeof($this->characterRepository->findAll()) == 0) {
            $apiConnection = new ApiConnectionHandler('all', array(), $this->em);
            $apiConnection->handleData();
        }
    }

    /**
     * @Route("/", name="frontpage")
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function index()
    {
        if ($this->connectionType == 'database') {
            $characters = $this->characterRepository->findAll();
        } else {
            $rickAndMortyApi = new ApiConnectionHandler('character', [], $this->em);
            $charactersPage = $rickAndMortyApi->handleData();

            $characters = $charactersPage[0];
            foreach ($charactersPage as $index => $page) {
                $characters += $page;
            }
        }

        return $this->render("frontpage.html.twig", [
            'characters' => $characters,
            'connectionType' => $this->connectionType,
        ]);
    }

    /**
     * @Route("/character/{id}", name="characterView")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function characterView($id)
    {
        if ($this->connectionType == 'database') {
            $character = $this->characterRepository->find($id);
        } else {
            $rmApi = new ApiConnectionHandler('character', ['id' => $id], $this->em);
            $charactersPage = $rmApi->handleData();

            $character = $charactersPage[0];
        }

        return $this->render('characterView.html.twig', [
            'character' => $character,
            'connectionType' => $this->connectionType,
        ]);
    }

    /**
     * @Route("/episode/{id}", name="episodeView")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function episodeView($id)
    {
        if ($this->connectionType == 'database') {
            $episode = $this->episodeRepository->find($id);
        } else {
            $rmApi = new ApiConnectionHandler('episode', ['id' => $id], $this->em);
            $episodePage = $rmApi->handleData();

            $episode = $episodePage[0];
        }

        return $this->render('episodeView.html.twig', [
            'episode' => $episode,
            'connectionType' => $this->connectionType,
        ]);
    }

    /**
     * @Route("/switch", name="connection_switch")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function ConnectionTypeSwitch()
    {
        $this->flipConnectionType();

        return $this->redirect('/');
    }

    private function flipConnectionType(){
        if (isset($_SESSION['database_connection'])) {
            $_SESSION['database_connection'] = !$_SESSION['database_connection'];
            $this->connectionType = ($_SESSION['database_connection'] ? 'database' : 'api');
        } else {
            $_SESSION['database_connection'] = false;
        }
    }
}
