<?php

namespace App\Controller\Product;

use App\Services\Product\ListServices;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class ListController extends AbstractController
{
    #[Route('/product', name: 'app_product_list')]
    public function index(Request $request, ListServices $services): JsonResponse
    {
        $srv = $services->call($request->query->all());
        $output = [
            'status' => $srv->status(),
        ];
        if ($srv->fail()) {
            $output['message'] = $srv->message();
            $output['error'] = $srv->result();
        } else {
            $output['data'] = $srv->result();
        }

        $response = new JsonResponse();
        $response->setStatusCode($srv->httpCode());
        $response->setData($output);

        return $response;
    }
}
