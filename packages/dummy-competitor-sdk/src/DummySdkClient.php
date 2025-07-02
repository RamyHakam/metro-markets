<?php

namespace DummySdk;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final  class DummySdkClient
{
    /**
     * This is a Fake SDK client simulating external competitor pricing SDK.
     *  Reads from a local JSON file and returns the entry for the given product id.
     * Returns a JsonResponse as a real HTTP client might.
     */

    private string $dataFile;
    public function __construct()
    {
   //     $this->dataFile = __DIR__ . '/Competitor-products-prices.json';
        // we can use this for retries failed fetches again
         $this->dataFile = __DIR__ . '/New-products-added.json';
    }

    public function getPrices(int $productId): JsonResponse
    {
        if (!file_exists($this->dataFile)) {
            return new JsonResponse(
                ['error' => 'Data file not found'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        $json = file_get_contents($this->dataFile);
        $items = json_decode($json, true);

        foreach ($items as $entry) {
            if (isset($entry['product_id']) && $entry['product_id'] === $productId) {
                return new JsonResponse(
                    $entry,
                    Response::HTTP_OK,
                    ['Content-Type' => 'application/json']
                );
            }
        }

        return new JsonResponse(
            ['error' => 'Product not found'],
            Response::HTTP_NOT_FOUND,
            ['Content-Type' => 'application/json']
        );
    }
}

