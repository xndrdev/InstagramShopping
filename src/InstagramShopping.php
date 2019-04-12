<?php declare(strict_types=1);

namespace SalesChannelTeam\Plugin\InstagramShopping;

use Doctrine\DBAL\Connection;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Api\Util\AccessKeyHelper;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\Framework\Plugin\Exception\PluginNotInstalledException;

class InstagramShopping extends Plugin
{


    public const PLUGIN_NAME = 'InstagramShopping';
    public const PLUGIN_NAME_DESCRIPTION = 'InstagramShopping Channel';

    public const SALES_CHANNEL_ID = 'd9410081ab13421abad6bc5056a87586';
    public const SALES_CHANNEL_TYPE_ID = '7aa2118749664a8b98d8ab9cbf34babd';

    public const ICON_NAME = 'default-communication-speech-bubble';
    public const CONFIG_NAMESPACE = 'instagram';
    public const ACCESS_TOKEN_NAME = 'access_token';
    public const CATALOG_ID_NAME = 'catalog_id';



    public function getAdministrationEntryPath(): string
    {
        return 'Administration';
    }

    public function install(InstallContext $context): void
    {
        try {
            $this->addMultiChannelTypes($context->getContext());
            $this->addMultiChannel($context->getContext());
        } catch (\Throwable $throwable) {
            echo '<pre>';
            print_r($throwable->getMessage());
            echo '</pre>';
        }
    }

    /**
     * @param UninstallContext $context
     */
    public function uninstall(UninstallContext $context): void
    {
        /** @var EntityRepositoryInterface $salesChannelRepository */
        $salesChannelRepository = $this->container->get('sales_channel.repository');
        $salesChannelRepository->delete(array(array('id' => 'd9410081ab13421abad6bc5056a87586')), $context->getContext());

        /** @var EntityRepositoryInterface $salesChannelTypeRepository */
        $salesChannelTypeRepository = $this->container->get('sales_channel_type.repository');
        $salesChannelTypeRepository->delete(array(array('id' => '7aa2118749664a8b98d8ab9cbf34babd')), $context->getContext());
    }

    private function addMultiChannelTypes(Context $context): void
    {
        $salesChannelTypeRepository = $this->container->get('sales_channel_type.repository');

        $salesChannelType = [
            'id'          => self::SALES_CHANNEL_TYPE_ID,
            'iconName'    => self::ICON_NAME,
            'name'        => self::PLUGIN_NAME,
            'description' => self::PLUGIN_NAME_DESCRIPTION
        ];

        $salesChannelTypeRepository->create([$salesChannelType], $context);
    }

    private function addMultiChannel(Context $context): void
    {
        $countryId        = $this->getDeDefaultCountryUuid();
        $paymentId        = $this->getDefaultPaymentUuid();
        $shippingMethodId = $this->getDefaultShippingUuid();

        $salesChannelRepository = $this->container->get('sales_channel.repository');

        $salesChannel = [
            'id'                 => self::SALES_CHANNEL_ID,
            'typeId'             => self::SALES_CHANNEL_TYPE_ID,
            'languageId'         => Defaults::LANGUAGE_SYSTEM,
            'currencyId'         => Defaults::CURRENCY,
            'paymentMethodId'    => $paymentId, //@ToDo use default if exists
            'shippingMethodId'   => $shippingMethodId,
            'countryId'          => $countryId,
            'name'               => 'InstagramShopping Channel',
            'active'             => false,
            'taxCalculationType' => 'vertical',
            'accessKey'          => AccessKeyHelper::generateAccessKey('sales-channel'),
            'customerGroupId'    => Defaults::FALLBACK_CUSTOMER_GROUP
        ];

        $salesChannelRepository->create([$salesChannel], $context);
    }

    private function getDefaultShippingUuid(): string
    {
        /** @var \Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface $repository */
        $repository = $this->container->get('shipping_method.repository');

        $entities = $repository->search((new Criteria())->addFilter(new EqualsFilter('active', 1)), Context::createDefaultContext());

        return $this->getInstallmentUuIdByEntities($entities);
    }

    /**
     * @return string
     * @throws PluginNotInstalledException
     * @throws \Shopware\Core\Framework\DataAbstractionLayer\Exception\InconsistentCriteriaIdsException
     */
    private function getDefaultPaymentUuid(): string
    {
        /** @var \Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface $repository */
        $repository = $this->container->get('payment_method.repository');

        $entities = $repository->search((new Criteria())->addFilter(new EqualsFilter('active', 1)), Context::createDefaultContext());

        return $this->getInstallmentUuIdByEntities($entities);
    }

    /**
     * @return string
     * @throws PluginNotInstalledException
     * @throws \Shopware\Core\Framework\DataAbstractionLayer\Exception\InconsistentCriteriaIdsException
     */
    private function getDeDefaultCountryUuid(): string
    {
        /** @var \Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface $repository */
        $repository = $this->container->get('country.repository');

        $entities = $repository->search((new Criteria())->addFilter(new EqualsFilter('iso', 'DE')), Context::createDefaultContext());

        return $this->getInstallmentUuIdByEntities($entities);
    }

    /**
     * @param EntitySearchResult $entitySearchResult
     *
     * @return string
     * @throws PluginNotInstalledException
     */
    final private function getInstallmentUuIdByEntities(EntitySearchResult $entitySearchResult): string
    {
        if ($entitySearchResult->getTotal() > 0) {
            foreach ($entitySearchResult->getIds() as $uUId) {
                return (string)$uUId;
            }
        } else {
            throw new PluginNotInstalledException($this->getName());
        }
    }
}
