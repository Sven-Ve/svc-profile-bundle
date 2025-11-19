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
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Svc\ProfileBundle\Validator\Constraints\ValidEmailDomain;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangeMailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
          ->add('email', EmailType::class, [
              'label' => 'New mail',
              'attr' => ['autofocus' => true],
              'constraints' => [
                  new NotBlank([
                      'message' => 'Please enter an email address',
                  ]),
                  new Email([
                      'message' => 'Please enter a valid email address',
                      'mode' => Email::VALIDATION_MODE_STRICT,
                  ]),
                  new Length([
                      'max' => 254, // RFC 5321 maximum email address length
                      'maxMessage' => 'Email address cannot be longer than {{ limit }} characters',
                  ]),
                  new ValidEmailDomain(),
              ],
          ])
          ->add('password', PasswordType::class, [
              'help' => 'Please enter your password to check your identity',
              'constraints' => [
                  new NotBlank([
                      'message' => 'Please enter a password',
                  ]),
              ],
              'toggle' => true,
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
