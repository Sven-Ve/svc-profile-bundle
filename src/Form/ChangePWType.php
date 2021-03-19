<?php

namespace Svc\ProfileBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaV3Type;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\IsTrueV3;

class ChangePWType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
      $builder
        ->add('password', PasswordType::class, [ 
          'help' => 'Please enter your old password to check your identity',
          "attr" => ["autofocus"=>true]
        ])
        ->add('plainPassword', PasswordType::class, [
          'label' => "New password",
          'help' => 'Please enter a new password',
          'mapped' => false,
          'constraints' => [
            new NotBlank(['message' => 'Please enter a password']),
            new Length([
              'min' => 6,
              'minMessage' => 'Your password should be at least {{ limit }} characters',
              'max' => 4096,
            ]),
          ],
      ]);

      if ($options['enableCaptcha']) {
        $builder->add('recaptcha', EWZRecaptchaV3Type::class, [ 
          "action_name" => "form",
          'constraints' => array(new IsTrueV3())
        ]);
      }
      
      $builder
        ->add('Change',SubmitType::class, ['attr' => ['class' => 'btn btn-lg btn-primary btn-block']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
          'enableCaptcha' => null,
          'translation_domain' => 'ProfileBundle'
          ]);
    }
}
