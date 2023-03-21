<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\ImportRepository;

use App\Entity\Objects;
use App\Entity\Fields;

/**
 * @Route("/api", name="api_")
 */
class ImportController extends AbstractController
{

    protected ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine) 
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @Route("/import", name="app_import", methods={"POST"})
     */
    public function index(): Response
    {
        try {
            $import = new ImportRepository($this->doctrine);
            $import->process();
    
            $data = [
                'message' => 'Process import completed.'
            ];
    
        } catch (\Exception $e) {

            $data = [
                'message' => $e->getMessage()
            ];

        }
        
        return $this->json($data);
    }

    // /**
    //  * @Route("/objects", name="app_import", methods={"GET"})
    //  */
    // public function getAllObjects()
    // {
    //     $data = $this->doctrine->getRepository(Objects::class)->findAll();

    //     return $this->json($data);
    // }

    // /**
    //  * @Route("/fields", name="app_import", methods={"GET"})
    //  */
    // public function getAllFields()
    // {
    //     $data = $this->doctrine->getRepository(Fields::class)->findAll();

    //     return $this->json($data); 
    // }
}
