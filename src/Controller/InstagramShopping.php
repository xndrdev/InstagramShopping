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
    const CATALOG_ID = '2301606873237604';
    const ACCESS_TOKEN = 'EAAF3cZAHi4fMBAFltokx4HWjLpvg2sozOcXnnON3znKcFe1xeAFyVuBhLd98tOYyCgOYGunTRscWlx4unun5HJhtUOXBUmUhHZCAfHqW7l8IPpPVvZASms0Vc6cM2X6LeBTZBoLniVHDsE9oqfkEzJJuCh0pRaORg5nZCG4sB2FtdFO0FJGBu';

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

        $curl = new cUrl;
        $url = 'https://graph.facebook.com/v3.2/'.self::CATALOG_ID.'/batch';
        
        $data = [
            "allow_upsert" => "true",
            "access_token" => self::ACCESS_TOKEN
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
                        "category" => "shopware_integration",
                        "description" => $entity->getDescription(),
                        "image_url" => $entity->getCover()->getMedia()->getUrl(),
                        "name" => $entity->getName(),
                        "price" => number_format($entity->getPrice()->getGross() * 100, 0, '.', ''),
                        "currency" => "EUR",
                        "condition" => "new",
                        "url" => 'http://shopware.test/detail/'.$entity->getId()
                    ]
                ];   
                
                $exportCounter++;
            }
        }

        $request = $curl->newRequest('post', $url, $data)->setHeader('Accept-Charset', 'utf-8');
        $request->send();

        return new JsonResponse(array('data' => $exportCounter));
    }

    /**
     * @Route("/api/v1/instagram-shopping-delete", name="instagram-shopping-delete", methods={"GET"})
     */
    public function instagramShoppingDelete() : JsonResponse
    {
        $curl = new cUrl;
        
        $response = '';
        $products = [];

        $url = 'https://graph.facebook.com/v3.2/'.self::CATALOG_ID.'/products?access_token='.self::ACCESS_TOKEN;
        $request = $curl->newRequest('get', $url)->setHeader('Accept-Charset', 'utf-8');
        $response = $request->send();

        if($response->body) {
            $data = json_decode($response->body);

            if(count($data->data)) {
                $products = array_merge($products, $data->data);
            }

            while(isset($data->paging) && isset($data->paging->next)) {
                $request = $curl->newRequest('get', $data->paging->next)->setHeader('Accept-Charset', 'utf-8');
                $response = $request->send();
                $data = json_decode($response->body);

                if(count($data->data)) {
                    $products = array_merge($products, $data->data);
                }
            }
        }

        $url = 'https://graph.facebook.com/v3.2/'.self::CATALOG_ID.'/batch';
        
        $response = '';
        $data = [
            "access_token" => self::ACCESS_TOKEN
        ];

        $deleteCounter = 0;

        if (count($products) > 0) {
            
            foreach ($products as $product) {
                $data["requests"][] = [
                    "method" => "DELETE",
                    "retailer_id" => $product->retailer_id,
                ];   
                
                $deleteCounter++;
            }
        }

        $request = $curl->newRequest('post', $url, $data)->setHeader('Accept-Charset', 'utf-8');
        $request->send();

        return new JsonResponse(array('data' => $deleteCounter));
    }

    /**
     * @Route("/api/v1/instagram-shopping-products", name="instagram-shopping-products", methods={"GET"})
     */
    public function instagramShoppingProducts() : JsonResponse
    {
        $curl = new cUrl;
        
        $response = '';
        $products = [];

        $url = 'https://graph.facebook.com/v3.2/'.self::CATALOG_ID.'/products?access_token='.self::ACCESS_TOKEN;
        $request = $curl->newRequest('get', $url)->setHeader('Accept-Charset', 'utf-8');
        $response = $request->send();

        if($response->body) {
            $data = json_decode($response->body);

            if(isset($data) && isset($data->data) && count($data->data) > 0) {
                $products = array_merge($products, $data->data);
            }

            while(isset($data->paging) && isset($data->paging->next)) {
                $request = $curl->newRequest('get', $data->paging->next)->setHeader('Accept-Charset', 'utf-8');
                $response = $request->send();
                $data = json_decode($response->body);

                if(count($data->data)) {
                    $products = array_merge($products, $data->data);
                }
            }
        }

        return new JsonResponse(array('data' => $products));
    }

    /**
     * @Route("/api/v1/instagram-shopping-local-products", name="instagram-shopping-local-products", methods={"GET"})
     */
    public function instagramShoppingLocalProducts() : JsonResponse
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

        $products = [];

        if ($entities->count() > 0) {
             /** @var \Shopware\Core\Content\Product\ProductEntity $entity */
             foreach ($entities as $entity) {
                $products[] = [
                    'name' => $entity->getName(),
                    'price' => $entity->getPrice()->getGross()
                ]; 
            }
        }

        return new JsonResponse(array('data' => $products));
    }
}
