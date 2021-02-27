<?php

namespace Svc\ProfileBundle\Controller;

use App\Entity\User;
use Svc\ProfileBundle\Entity\UserChanges;
use Svc\ProfileBundle\Form\ChangeMailType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class ChangeMailController extends AbstractController
{
  public function startForm(Request $request): Response
  {
    $user = new User();
    $form = $this->createForm(ChangeMailType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      
      $change = new UserChanges();
      $change->setUser($this->getUser());
      $change->setChangeType(1);
      $change->setNewMail($user->getEmail());

      unset($user);

      $entityManager = $this->getDoctrine()->getManager();
      $entityManager->persist($change);
      $entityManager->flush();

      die ("Gespeichert");
      return $this->redirectToRoute('homeNoLocale');
    }

    return $this->render('@SvcProfile/profile/changeMail/start.html.twig', [
        'user' => $user,
        'form' => $form->createView(),
    ]);
  }
}
