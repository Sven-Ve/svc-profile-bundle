<?php

namespace Svc\ProfileBundle\Controller;

use App\Security\CustomAuthenticator;
use Svc\ProfileBundle\Form\ChangeMailType;
use Svc\ProfileBundle\Service\ChangeMailHelper;
use Svc\UtilBundle\Service\MailerHelper;
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


  public function startForm(Request $request, CustomAuthenticator $customAuth, MailerHelper $mailHelper): Response
  { 
    $user = $this->getUser();
    if (!$user) {
      $this->addFlash("warning", "Please login before changing email address.");
      return ($this->redirectToRoute("app_login"));
      exit;
    }

    $form = $this->createForm(ChangeMailType::class);
    $form->handleRequest($request);
    $newMail = trim($form->get('email')->getData());


    if ($form->isSubmitted() && $form->isValid()) {
      if (strtolower($user->getEmail()) == strtolower($newMail)) {
        $this->addFlash("danger","You have to enter a new mail address. $newMail is your old one.");
        return ($this->redirectToRoute("svc_profile_change_mail_start"));
        exit;
      }

      if (!$this->helper->checkExpiredRequest($user)) {
        $this->addFlash("danger","You requested already a mail change. Please check your mail to confirm it.");
        return ($this->redirectToRoute("svc_profile_change_mail_start"));
        exit;
      }

      if ($this->helper->checkMailExists($newMail)) {
        $this->addFlash("danger","Mail address $newMail already exists. Please choose another on");
        return ($this->redirectToRoute("svc_profile_change_mail_start"));
      }

      $credential = [ 'password' => $form->get('password')->getData()];
      if ($customAuth->checkCredentials($credential, $user))
      {

        $this->helper->writeUserChangeRecord($user, $newMail);

        if (!$this->helper->sendActivationMail($newMail)) {
          $this->addFlash("danger", "Cannot send mail to $newMail. Address exists?");
          return ($this->redirectToRoute("svc_profile_change_mail_start"));
          exit;  
        }
        return ($this->redirectToRoute("svc_profile_change_mail_sent1", ['newmail' => $newMail]));
      } else {
        $this->addFlash("danger", "Wrong password");
        return ($this->redirectToRoute("svc_profile_change_mail_start"));
        exit;
        }
    }

    return $this->render('@SvcProfile/profile/changeMail/start.html.twig', [
        'form' => $form->createView()
    ]);
  }

  public function mail1Sent(Request $request): Response {
    $newMail=$_GET['newmail'] ?? '?';
    return $this->render('@SvcProfile/profile/changeMail/mail1_sent.html.twig', [
      'newMail' => $newMail
    ]);
  }

  public function activateNewMail(Request $request): Response {
    $token=$_GET['token'] ?? '?';
    if (!$this->helper->activateNewMail($token)) {
      $this->addFlash("danger", "Request is expired or not found. Please start again");
      return ($this->redirectToRoute("svc_profile_change_mail_start"));
      exit;
    }

    $this->addFlash("success", "Your new mail is activated. Please re-login.");
    return ($this->redirectToRoute("app_login"));

  }
}
