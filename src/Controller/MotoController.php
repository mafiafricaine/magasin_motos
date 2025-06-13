<?php

namespace App\Controller;

use App\Entity\Moto;
use App\Form\MotoForm;
use App\Repository\MotoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


final class MotoController extends AbstractController
{
    #[Route(path: "/",name: 'app_moto_index', methods: ['GET'])]
    public function index(MotoRepository $motoRepository): Response
    {
        return $this->render('moto/index.html.twig', [
            'motos' => $motoRepository->findAll(),
        ]);
    }

    #[Route('/moto/creer', name: 'app_moto_create', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $moto = new Moto();
        $form = $this->createForm(MotoForm::class, $moto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($moto);
            $entityManager->flush();
            $this->addFlash('success', 'La moto ' . $moto->getNom(). 'a bien été créée');

            return $this->redirectToRoute('app_moto_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('moto/new.html.twig', [
            'moto' => $moto,
            'form' => $form,
        ]);
    }

    #[Route('/moto/{id}/details', name: 'app_moto_show', methods: ['GET'])]
    public function show(Moto $moto): Response
    {
        
        return $this->render('moto/show.html.twig', [
            'moto' => $moto,
        ]);
    }

    #[Route('/moto/{id}/editer', name: 'app_moto_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Moto $moto, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MotoForm::class, $moto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'La moto a bien été modifiée');

            return $this->redirectToRoute('app_moto_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('moto/edit.html.twig', [
            'moto' => $moto,
            'form' => $form,
        ]);
    }

    #[Route('/moto/{id}/supprimer', name: 'app_moto_delete')]
    public function delete(Request $request, Moto $moto, EntityManagerInterface $entityManager): Response
    {
        $nom = $moto->getNom();
        if ($this->isCsrfTokenValid('delete'.$moto->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($moto);
            $entityManager->flush();
            $this->addFlash('info', 'La recette ' . $nom . ' a bien été supprimée');

        }

        return $this->redirectToRoute('app_moto_index', [], Response::HTTP_SEE_OTHER);
    }
}
