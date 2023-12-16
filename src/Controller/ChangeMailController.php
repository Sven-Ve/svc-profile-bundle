<?php

namespace Svc\ProfileBundle\Controller;

use Svc\ProfileBundle\Form\ChangeMailType;
use Svc\ProfileBundle\Service\ChangeMailHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Controller for change mail in user entity.
 *
 * @author Sven Vetter <dev@sv-systems.com>
 */
class ChangeMailController extends AbstractController
{
  public function __construct(private bool $enableCaptcha, private ChangeMailHelper $helper, private TranslatorInterface $translator)
  {
  }

  /**
   * Display and handle a form to start the process of changing the mail address.
   */
  public function startForm(Request $request, UserPasswordHasherInterface $passwordHasher): Response
  {
    $user = $this->getUser();
    if (!$user) {
      $this->addFlash('warning', $this->t('Please login before changing email address.'));

      return $this->redirectToRoute('app_login');
    }

    $form = $this->createForm(ChangeMailType::class, null, ['enableCaptcha' => $this->enableCaptcha]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $newMail = trim($form->get('email')->getData());
      if (strtolower($user->getEmail()) == strtolower($newMail)) { /* @phpstan-ignore-line */
        $this->addFlash(
          'danger',
          $this->t("You have to enter a new mail address. 'newMail' is your old one.", ['newMail' => $newMail])
        );

        return $this->redirectToRoute('svc_profile_change_mail_start');
      }

      if (!$this->helper->checkExpiredRequest($user)) {  /* @phpstan-ignore-line */
        $this->addFlash('danger', $this->t('You requested already a mail change. Please check your mail to confirm it.'));

        return $this->redirectToRoute('svc_profile_change_mail_start');
      }

      if ($this->helper->checkMailExists($newMail)) {
        $this->addFlash('danger', "Mail address $newMail already exists. Please choose another on");

        return $this->redirectToRoute('svc_profile_change_mail_start');
      }

      $credential = $form->get('password')->getData();

      /* @phpstan-ignore-next-line */
      if ($passwordHasher->isPasswordValid($user, $credential)) {
        $this->helper->writeUserChangeRecord($user, $newMail);  /* @phpstan-ignore-line */

        if (!$this->helper->sendActivationMail($newMail)) {
          $this->addFlash('danger', "Cannot send mail to $newMail. Address exists?");

          return $this->redirectToRoute('svc_profile_change_mail_start');
        }

        return $this->redirectToRoute('svc_profile_change_mail_sent1', ['newmail' => $newMail]);
      } else {
        $this->addFlash('danger', $this->t('Wrong password, please try again!'));

        return $this->redirectToRoute('svc_profile_change_mail_start');
      }
    }

    return $this->render('@SvcProfile/profile/changeMail/start.html.twig', [
      'form' => $form,
    ]);
  }

  /**
   * info page about sending the first mail.
   */
  public function mail1Sent(Request $request): Response
  {
    $newMail = $_GET['newmail'] ?? '?';

    return $this->render('@SvcProfile/profile/changeMail/mail1_sent.html.twig', [
      'newMail' => $newMail,
    ]);
  }

  /**
   * Public method to activate the new mail address via token.
   */
  public function activateNewMail(Request $request): Response
  {
    $token = $_GET['token'] ?? '?';
    if (!$this->helper->activateNewMail($token)) {
      $this->addFlash('danger', $this->t('Request is expired or not found. Please start again'));

      return $this->redirectToRoute('svc_profile_change_mail_start');
    }

    $this->addFlash('success', $this->t('Your new mail is activated. Please re-login.'));

    return $this->redirectToRoute('app_login');
  }

  /**
   * private function to translate content in namespace 'ProfileBundle'.
   */
  private function t(string $text, array $placeholder = []): string
  {
    return $this->translator->trans($text, $placeholder, 'ProfileBundle');
  }
}
