<?php

namespace Svc\ProfileBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaV3Type;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\IsTrueV3;


class ChangeMailType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
      $builder
        ->add('email', EmailType::class, ['label' => 'New mail'])
        ->add('password', PasswordType::class, [ 'help' => 'Please enter your password to check your identity']);

      if ($options['enableCaptcha']) {
        $builder->add('recaptcha', EWZRecaptchaV3Type::class, [ 
          "action_name" => "form",
          'constraints' => array(new IsTrueV3())
        ]);
      }
        
      $builder
        ->add('Save',SubmitType::class, ['attr' => ['class' => 'btn btn-lg btn-primary btn-block']])
      ;
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults(['enableCaptcha' => null]);
  }
}
