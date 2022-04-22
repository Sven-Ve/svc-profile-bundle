<?php

namespace Svc\ProfileBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Svc\ProfileBundle\Form\ChangePWType;
use Svc\UtilBundle\Service\EnvInfoHelper;
use Svc\UtilBundle\Service\MailerHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Controller for change password
 *
 * @author Sven Vetter <dev@sv-systems.com>
 */
class ChangePWController extends AbstractController
{

  private $mailerHelper;
  private $enableCaptcha;
  private $translator;

  public function __construct(MailerHelper $mailerHelper, $enableCaptcha, TranslatorInterface $translator)
  {
    $this->mailerHelper = $mailerHelper;
    $this->enableCaptcha = $enableCaptcha;
    $this->translator = $translator;
  }

  /**
   * Display and handle a form to start the process of changing the password
   *
   * @param Request $request
   * @param UserPasswordHasherInterface $passwordHasher
   * @return Response
   */
  public function startForm(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
  {
    $user = $this->getUser();
    if (!$user) {
      $this->addFlash("warning", $this->t("Please login before changing password."));
      return ($this->redirectToRoute("app_login"));
    }

    $form = $this->createForm(ChangePWType::class, null, ['enableCaptcha' => $this->enableCaptcha]);
    $form->handleRequest($request);
    $user = $this->getUser();


    if ($form->isSubmitted() && $form->isValid()) {

      $oldPW =  $form->get('password')->getData();
      if ($passwordHasher->isPasswordValid($user, $oldPW)) { /** @phpstan-ignore-line */
        $newPW = trim($form->get('plainPassword')->getData());
        $user->setPassword($passwordHasher->hashPassword($user, $newPW)); /** @phpstan-ignore-line */

        $entityManager->persist($user);
        $entityManager->flush();
        if ($this->sendPasswordChangedMail($user->getEmail())) { /** @phpstan-ignore-line */
          $this->addFlash("success", $this->t("Password changed, please login"));
        } else {
          $this->addFlash("warning", $this->t("Password changed, please login") . ". But cannot send info mail to " . $user->getEmail()); /** @phpstan-ignore-line */
        }

        return ($this->redirectToRoute("app_login"));
      } else {
        $this->addFlash("danger", $this->t("Wrong password, please try again!"));
        return ($this->redirectToRoute("svc_profile_change_pw_start"));
      }
    }

    return $this->render('@SvcProfile/profile/changePW/start.html.twig', ['form' => $form->createView()]);
  }

  /**
   * send a mail with info anout password change
   *
   * @param string $mail email address to send
   * @return bool
   */
  public function sendPasswordChangedMail(string $mail): bool
  {
    $url = EnvInfoHelper::getURLtoIndexPhp();
    $html = $this->renderView("@SvcProfile/profile/changePW/MT_pwChanged.html.twig", ["startPage" => $url, "mail" => $mail]);
    $text = $this->renderView("@SvcProfile/profile/changePW/MT_pwChanged.text.twig", ["startPage" => $url, "mail" => $mail]);
    return $this->mailerHelper->send($mail, $this->t("Password changed"), $html, $text);
  }

  /**
   * private function to translate content in namespace 'ProfileBundle'
   *
   * @param string $text
   * @return string
   */
  private function t(string $text): string
  {
    return $this->translator->trans($text, [], 'ProfileBundle');
  }
}
