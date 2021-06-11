<?php

namespace Svc\ProfileBundle\Controller;

use App\Security\CustomAuthenticator;
use Svc\ProfileBundle\Form\ChangeMailType;
use Svc\ProfileBundle\Service\ChangeMailHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Controller for change mail in user entity
 *
 * @author Sven Vetter <dev@sv-systems.com>
 */
class ChangeMailController extends AbstractController
{

  private $helper;
  private $enableCaptcha;
  private $translator;

  public function __construct(ChangeMailHelper $helper, bool $enableCaptcha, TranslatorInterface $translator)
  {
    $this->helper = $helper;
    $this->enableCaptcha = $enableCaptcha;
    $this->translator = $translator;
  }

  /**
   * Display and handle a form to start the process of changing the mail address
   *
   * @param Request $request
   * @param CustomAuthenticator $customAuth
   * @return Response
   */
  public function startForm(Request $request, CustomAuthenticator $customAuth): Response
  {
    $user = $this->getUser();
    if (!$user) {
      $this->addFlash("warning", $this->t("Please login before changing email address."));
      return ($this->redirectToRoute("app_login"));
      exit;
    }

    $form = $this->createForm(ChangeMailType::class, null, ['enableCaptcha' => $this->enableCaptcha]);
    $form->handleRequest($request);
    $newMail = trim($form->get('email')->getData());


    if ($form->isSubmitted() && $form->isValid()) {
      if (strtolower($user->getEmail()) == strtolower($newMail)) {
        $this->addFlash(
          "danger",
          $this->t("You have to enter a new mail address. 'newMail' is your old one.", ['newMail' => $newMail])
        );
        return ($this->redirectToRoute("svc_profile_change_mail_start"));
        exit;
      }

      if (!$this->helper->checkExpiredRequest($user)) {
        $this->addFlash("danger", $this->t("You requested already a mail change. Please check your mail to confirm it."));
        return ($this->redirectToRoute("svc_profile_change_mail_start"));
        exit;
      }

      if ($this->helper->checkMailExists($newMail)) {
        $this->addFlash("danger", "Mail address $newMail already exists. Please choose another on");
        return ($this->redirectToRoute("svc_profile_change_mail_start"));
      }

      $credential = ['password' => $form->get('password')->getData()];
      if ($customAuth->checkCredentials($credential, $user)) {

        $this->helper->writeUserChangeRecord($user, $newMail);

        if (!$this->helper->sendActivationMail($newMail)) {
          $this->addFlash("danger", "Cannot send mail to $newMail. Address exists?");
          return ($this->redirectToRoute("svc_profile_change_mail_start"));
          exit;
        }
        return ($this->redirectToRoute("svc_profile_change_mail_sent1", ['newmail' => $newMail]));
      } else {
        $this->addFlash("danger", $this->t("Wrong password, please try again!"));
        return ($this->redirectToRoute("svc_profile_change_mail_start"));
        exit;
      }
    }

    return $this->render('@SvcProfile/profile/changeMail/start.html.twig', [
      'form' => $form->createView()
    ]);
  }

  /**
   * info page about sending the first mail
   *
   * @param Request $request
   * @return Response
   */
  public function mail1Sent(Request $request): Response
  {
    $newMail = $_GET['newmail'] ?? '?';
    return $this->render('@SvcProfile/profile/changeMail/mail1_sent.html.twig', [
      'newMail' => $newMail
    ]);
  }

  /**
   * Public method to activate the new mail address via token
   *
   * @param Request $request
   * @return Response
   */
  public function activateNewMail(Request $request): Response
  {
    $token = $_GET['token'] ?? '?';
    if (!$this->helper->activateNewMail($token)) {
      $this->addFlash("danger", $this->t("Request is expired or not found. Please start again"));
      return ($this->redirectToRoute("svc_profile_change_mail_start"));
      exit;
    }

    $this->addFlash("success", $this->t("Your new mail is activated. Please re-login."));
    return ($this->redirectToRoute("app_login"));
  }

  /**
   * private function to translate content in namespace 'ProfileBundle'
   *
   * @param string $text
   * @param array $placeholder
   * @return string
   */
  private function t(string $text, array $placeholder = []): string
  {
    return $this->translator->trans($text, $placeholder, 'ProfileBundle');
  }
}
