<?php

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Controller;

use BitBag\ShopwareAppSystemBundle\Exception\ShopNotFoundException;
use BitBag\ShopwareAppSystemBundle\Factory\Context\ContextFactoryInterface;
use BitBag\ShopwareAppSystemBundle\Repository\ShopRepositoryInterface;
use BitBag\ShopwarePocztaPolskaApp\Entity\Config;
use BitBag\ShopwarePocztaPolskaApp\Finder\SalesChannelFinderInterface;
use BitBag\ShopwarePocztaPolskaApp\Form\Type\ConfigType;
use BitBag\ShopwarePocztaPolskaApp\Repository\ConfigRepositoryInterface;
use BitBag\ShopwarePocztaPolskaApp\Resolver\ApiResolverInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;
use Vin\ShopwareSdk\Data\Context;
use Vin\ShopwareSdk\Data\Entity\SalesChannel\SalesChannelEntity;

final class ConfigurationModuleController extends AbstractController
{
    public function __construct(
        private ConfigRepositoryInterface $configRepository,
        private EntityManagerInterface $entityManager,
        private ShopRepositoryInterface $shopRepository,
        private TranslatorInterface $translator,
        private SalesChannelFinderInterface $salesChannelFinder,
        private ContextFactoryInterface $contextFactory,
        private ApiResolverInterface $apiResolver
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $shopId = $request->query->get('shop-id', '');
        $shop = $this->shopRepository->find($shopId);
        if (null === $shop) {
            throw new ShopNotFoundException($shopId);
        }

        $context = $this->contextFactory->create($shop);
        if (null === $context) {
            throw new UnauthorizedHttpException('');
        }

        $config = $this->configRepository->findByShopIdAndSalesChannelId($shopId, '') ?? new Config();

        if ($request->isMethod('POST')) {
            $salesChannelId = (string) $request->request->get('salesChannelId');

            $config = $this->configRepository->findByShopIdAndSalesChannelId($shopId, $salesChannelId) ?? new Config();
        }

        $form = $this->createForm(ConfigType::class, $config, [
            'salesChannels' => $this->getSalesChannelsForForm($context),
            'originOffices' => $this->getoriginOffices($shopId, ''),
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $config->setShop($shop);

            $this->entityManager->persist($config);
            $this->entityManager->flush();

            $session = $request->getSession();
            $session->getFlashBag()->add('success', $this->translator->trans('bitbag.shopware_poczta_polska_app.config.saved'));
        }

        return $this->renderForm('configuration_module/index.html.twig', [
            'form' => $form,
        ]);
    }

    private function getSalesChannelsForForm(Context $context): array
    {
        $salesChannels = $this->salesChannelFinder->findAll($context)->getEntities()->getElements();

        $items = [];

        /** @var SalesChannelEntity $salesChannel */
        foreach ($salesChannels as $salesChannel) {
            if (null !== $salesChannel->name) {
                $items[$salesChannel->name] = $salesChannel->id;
            }
        }

        return array_merge(
            [$this->translator->trans('bitbag.shopware_poczta_polska_app.config.sales_channels') => ''],
            $items
        );
    }

    private function getOriginOffices(string $shopId, string $salesChannelId): array
    {
        $client = $this->apiResolver->getClient($shopId, $salesChannelId);
        $originOffices = $client->getOriginOffice()->getOriginOffice();
        $items = [];

        foreach ($originOffices as $originOffice) {
            $items[$originOffice->getName()] = $originOffice->getId();
        }

        return $items;
    }
}
