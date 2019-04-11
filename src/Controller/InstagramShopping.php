<?php declare(strict_types=1);

namespace SalesChannelTeam\Plugin\InstagramShopping\Controller;

require_once __DIR__ . '/../../vendor/autoload.php';

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Shopware\Core\Framework\Context as Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;

use anlutro\cURL\cURL as cUrl;

class InstagramShopping extends AbstractController
{
    /**
     * @Route("/api/v1/instagram-shopping-export", name="instagram-shopping-export", methods={"GET"})
     */
    public function instagramShoppingExport() : JsonResponse
    {
        /** @var EntityRepositoryInterface $saleschannelRepository */
        $saleschannelRepository = $this->container->get('sales_channel.repository');

        /** @var \Shopware\Core\System\SalesChannel\SalesChannelEntity $salesChannelEntity */
        $salesChannelEntity = $saleschannelRepository->search(new Criteria([
            'd9410081ab13421abad6bc5056a87586'
        ]), \Shopware\Core\Framework\Context::createDefaultContext())->first();

        /** @var EntityRepositoryInterface $productRepository */
        $productRepository = $this->container->get('product.repository');

        /** @var EntityCollection $entities */
        $entities = $productRepository->search((new Criteria())->addFilter(new EqualsFilter('product.visibilities.salesChannelId', $salesChannelEntity->getId())), Context::createDefaultContext());

        $catalogId = '';
        $accessToken = '';

        $curl = new cUrl;
        $url = 'https://graph.facebook.com/v3.2/'.$catalogId.'/batch';
        
        $response = '';
        $data = [
            "allow_upsert" => "true",
            "access_token" => $accessToken
        ];

        if ($entities->count() > 0) {
            
            $exportCounter = 0;
            
            /** @var \Shopware\Core\Content\Product\ProductEntity $entity */
            foreach ($entities as $entity) {
                $data["requests"][] = [
                    "method" => "UPDATE",
                    "retailer_id" => $entity->getId(),
                    "data" => [
                        "availability" => "in stock",
                        "brand" => $entity->getManufacturer()->getName(),
                        "category" => "shopware_6_integration",
                        "description" => $entity->getDescription(),
                        "image_url" => $entity->getCover()->getMedia()->getUrl(),
                        "name" => $entity->getName(),
                        "price" => number_format($entity->getPrice()->getGross() * 100, 0, '.', ''),
                        "currency" => "EUR",
                        "condition" => "new",
                        "url" => 'http://shopware6.test/detail/'.$entity->getId()
                    ]
                ];   
                
                $exportCounter++;
            }
        }

        $request = $curl->newRequest('post', $url, $data)->setHeader('Accept-Charset', 'utf-8');
        $response = $request->send();

        return new JsonResponse(array('data' => $exportCounter));
    }
}