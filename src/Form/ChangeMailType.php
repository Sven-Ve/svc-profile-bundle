<?php

namespace Svc\ProfileBundle\Form;

use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaV3Type;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\IsTrueV3;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChangeMailType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('email', EmailType::class, ['label' => 'New mail', 'attr' => ['autofocus' => true]])
      ->add('password', PasswordType::class, ['help' => 'Please enter your password to check your identity']);

    if ($options['enableCaptcha']) {
      /* @phpstan-ignore-next-line */
      $builder->add('recaptcha', EWZRecaptchaV3Type::class, [
        'action_name' => 'form',
        /* @phpstan-ignore-next-line */
        'constraints' => [new IsTrueV3()],
      ]);
    }

    $builder
      ->add('Change', SubmitType::class, ['attr' => ['class' => 'btn btn-lg btn-primary btn-block']]);
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults([
      'enableCaptcha' => null,
      'translation_domain' => 'ProfileBundle',
    ]);
  }
}
