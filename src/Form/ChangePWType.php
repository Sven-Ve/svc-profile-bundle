<?php

declare(strict_types=1);

/*
 * This file is part of the svc/profile-bundle.
 *
 * (c) 2025 Sven Vetter <dev@sv-systems.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Svc\ProfileBundle\Form;

use Karser\Recaptcha3Bundle\Form\Recaptcha3Type;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3;
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
          ->add('password', PasswordType::class, [
              'help' => 'Please enter your old password to check your identity',
              'attr' => ['autofocus' => true],
              'toggle' => true,
          ])
          ->add('plainPassword', PasswordType::class, [
              'label' => 'New password',
              'help' => 'Please enter a new password',
              'mapped' => false,
              'toggle' => true,
              'constraints' => [
                  new NotBlank(['message' => 'Please enter a password']),
                  new Length([
                      'min' => 8,
                      'minMessage' => 'Your password should be at least {{ limit }} characters',
                      'max' => 4096,
                  ]),
              ],
          ]);

        if ($options['enableCaptcha']) {
            /* @phpstan-ignore-next-line */
            $builder->add('captcha', Recaptcha3Type::class, [
                /* @phpstan-ignore-next-line */
                'constraints' => new Recaptcha3(),
                'action_name' => 'homepage',
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
