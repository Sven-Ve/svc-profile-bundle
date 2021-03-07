<?php

namespace Svc\ProfileBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangePWType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('password', PasswordType::class, [ 'help' => 'Please enter your old password to check your identity'])
            ->add('plainPassword', PasswordType::class, [
              'label' => "New password",
              'help' => 'Please enter a new password',
              'mapped' => false,
              'constraints' => [
                  new NotBlank([
                      'message' => 'Please enter a password',
                  ]),
                  new Length([
                      'min' => 6,
                      'minMessage' => 'Your password should be at least {{ limit }} characters',
                      // max length allowed by Symfony for security reasons
                      'max' => 4096,
                  ]),
              ],
          ])

            ->add('Change',SubmitType::class, ['attr' => ['class' => 'btn btn-lg btn-primary btn-block']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }
}
