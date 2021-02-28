<?php

namespace Svc\ProfileBundle\Controller;

use App\Security\CustomAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Svc\ProfileBundle\Entity\UserChanges;
use Svc\ProfileBundle\Form\ChangeMailType;
use Svc\ProfileBundle\Repository\UserChangesRepository;
use Svc\ProfileBundle\Service\ChangeMailHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class ChangeMailController extends AbstractController
{

  private const TOKENLIFETIME = 3600;

  private $customAuth;
  private $helper;

  /*
  public function __construct(CustomAuthenticator $customAuth)
  {
    $this->customAuth = $customAuth;
  }
*/

  public function startForm(Request $request, ChangeMailHelper $helper, EntityManagerInterface $em, CustomAuthenticator $customAuth, UserChangesRepository $rep): Response
  {

  //  $rep = $em->getRepository(UserChanges::class);
  //  dump($rep->findAll());

    $helper->checkExpiredRequest($this->getUser());
//    die;
    $form = $this->createForm(ChangeMailType::class);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

      $credential = [ 'password' => $form->get('password')->getData()];
      $user = $this->getUser();
      if ($this->customAuth->checkCredentials($credential, $user))
      {
        $expiresAt = new \DateTimeImmutable(\sprintf('+%d seconds', static::TOKENLIFETIME));

        $change = new UserChanges();
        $change->setUser($user);
        $change->setChangeType(1);
        $change->setNewMail($form->get('email')->getData());
        $change->setExpiresAt($expiresAt);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($change);
        $entityManager->flush();

        die ("Gespeichert");
        return $this->redirectToRoute('homeNoLocale');
      } else {
        $this->addFlash("danger", "Wrong password");
      }
    }

    return $this->render('@SvcProfile/profile/changeMail/start.html.twig', [
        'form' => $form->createView()
    ]);
  }
}
