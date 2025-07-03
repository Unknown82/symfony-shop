<?php

namespace App\Controller;


use App\Form\ProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CustomerController extends AbstractController
{
    #[Route('/customer', name: 'app_customer')]
    public function index(): Response
    {
        return $this->render('customer/index.html.twig', [
            'controller_name' => 'CustomerController',
        ]);
    }
    #[Route('/edit', name: 'profile_edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Profil erfolgreich aktualisiert.');
            return $this->redirectToRoute('profile_edit');
        }
        return $this->render('customer/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
