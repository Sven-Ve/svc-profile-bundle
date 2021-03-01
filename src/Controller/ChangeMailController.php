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

/**
 * Controller for change mail in user
 *
 * @author Sven Vetter <dev@sv-systems.com>
 */
class ChangeMailController extends AbstractController
{

  private const TOKENLIFETIME = 3600;

  private $helper;

  
  public function __construct(ChangeMailHelper $helper)
  {
    $this->helper = $helper;
  }


  public function startForm(Request $request, EntityManagerInterface $em, CustomAuthenticator $customAuth, UserChangesRepository $rep): Response
  {

  

    
    $form = $this->createForm(ChangeMailType::class);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      if (!$this->helper->checkExpiredRequest($this->getUser())) {
        $this->addFlash("danger","You requested already a mail change. Please check your mail to confirm it.");
        return ($this->redirectToRoute("svc_profile_change_mail_start"));
        exit;
      }

      $credential = [ 'password' => $form->get('password')->getData()];
      $user = $this->getUser();
      if ($customAuth->checkCredentials($credential, $user))
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
