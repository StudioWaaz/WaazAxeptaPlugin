<?php

/**
 * This file was created by the developers from Waaz.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://www.studiowaaz.com and write us
 * an email on developpement@studiowaaz.com.
 */

namespace Waaz\AxeptaPlugin\Form\Type;

use Waaz\AxeptaPlugin\Legacy\Mercanet;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @author Ibes Mongabure <developpement@studiowaaz.com>
 */
final class AxeptaGatewayConfigurationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('merchant_id', TextType::class, [
              'label' => 'waaz.axepta.merchant_id',
              'constraints' => [
                new NotBlank([
                  'message' => 'waaz.axepta.merchant_id.not_blank',
                  'groups' => ['sylius']
                ])
              ],
            ])
            ->add('hmac_key', TextType::class, [
                'label' => 'waaz.axepta.hmac_key',
                'constraints' => [
                    new NotBlank([
                        'message' => 'waaz.axepta.hmac_key.not_blank',
                        'groups' => ['sylius']
                    ])
                ],
            ])
            ->add('blowfish_key', TextType::class, [
                'label' => 'waaz.axepta.blowfish_key',
                'constraints' => [
                    new NotBlank([
                        'message' => 'waaz.axepta.blowfish_key.not_blank',
                        'groups' => ['sylius']
                    ])
                ],
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $data = $event->getData();
                $data['payum.http_client'] = '@waaz.axepta.bridge.axepta_bridge';
                $event->setData($data);
            })
        ;
    }
}
