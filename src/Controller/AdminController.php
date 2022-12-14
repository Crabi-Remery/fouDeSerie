<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Serie;
use App\Form\SerieType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class AdminController extends AbstractController
{
    #[Route('/admin/series', name: 'app_admin_addSerie')]
    public function addSerie(Request $request, ManagerRegistry $doctrine): Response
    {
        $serie = new Serie();
        $form=$this->createForm(SerieType::class, $serie); 
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $entityManager=$doctrine->getManager();
            $entityManager->persist($serie);
            $entityManager->flush();

            return $this->redirectToRoute('app_series');
        }

        return $this->render('admin/addSerie.html.twig', ['form' => $form->createView(),]);
    }

    #[Route('/admin/series/{id}', name: 'app_admin_updateSerie')]
    public function updateSerie(Request $request, ManagerRegistry $doctrine, $id): Response
    {
        $repository = $doctrine->getRepository(Serie::class);
        $laSerie = $repository->find($id);
        if ($laSerie !== null) {
            $updateForm=$this->createForm(SerieType::class, $laSerie); 
            $updateForm->handleRequest($request);
            if($updateForm->isSubmitted() && $updateForm->isValid()) {
                $entityManager=$doctrine->getManager();
                $entityManager->persist($laSerie);
                $entityManager->flush();
    
                return $this->redirectToRoute('app_series');
            }
            return $this->render('admin/addSerie.html.twig', ['form' => $updateForm->createView(),]);
        } else {
            return new JsonResponse(['message' => 'Serie not found'], Response::HTTP_NOT_FOUND);
        }
    }
}
