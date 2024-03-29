<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Form\Type;

use BitBag\ShopwarePocztaPolskaApp\Entity\Config;
use BitBag\ShopwarePocztaPolskaApp\Entity\ConfigInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ConfigType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('salesChannelId', ChoiceType::class, [
                'label' => 'bitbag.shopware_poczta_polska_app.config.sales_channel',
                'required' => false,
                'choices' => $options['salesChannels'],
            ])
            ->add('apiLogin', TextType::class, [
                'label' => 'bitbag.shopware_poczta_polska_app.config.api_login',
                'required' => true,
            ])
            ->add('apiPassword', PasswordType::class, [
                'label' => 'bitbag.shopware_poczta_polska_app.config.api_password',
                'required' => true,
            ])
            ->add('apiEnvironment', ChoiceType::class, [
                'label' => 'bitbag.shopware_poczta_polska_app.config.api_environment',
                'required' => true,
                'choices' => [
                    'bitbag.shopware_poczta_polska_app.config.production_environment' => ConfigInterface::PRODUCTION_ENVIRONMENT,
                    'bitbag.shopware_poczta_polska_app.config.sandbox_environment' => ConfigInterface::SANDBOX_ENVIRONMENT,
                ],
            ])
            ->add('originOffice', ChoiceType::class, [
                'label' => 'bitbag.shopware_poczta_polska_app.config.office_origin',
                'required' => false,
                'choices' => $options['originOffices'],
                'placeholder' => 'bitbag.shopware_poczta_polska_app.config.origin_offices.select',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Config::class,
            'salesChannels' => [],
            'originOffices' => [],
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }

    public function getName(): string
    {
        return 'config';
    }
}
