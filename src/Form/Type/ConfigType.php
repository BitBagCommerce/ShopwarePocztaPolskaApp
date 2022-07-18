<?php

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Form\Type;

use BitBag\ShopwarePocztaPolskaApp\Entity\Config;
use BitBag\ShopwarePocztaPolskaApp\Entity\ConfigInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
            ->add('apiPassword', TextType::class, [
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
            ->add('officeOrigin', ChoiceType::class, [
                'label' => 'bitbag.shopware_poczta_polska_app.config.office_origin',
                'required' => true,
                'choices' => $options['officeOrigins'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Config::class,
            'salesChannels' => [],
            'officeOrigins' => [],
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
