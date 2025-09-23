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
        if ($srv->fail()) {
            $output['error'] = $srv->result();
        } else {
            $output['data'] = $srv->result();
        }

        $output['message'] = $srv->message();
        $output['status'] = $srv->status();

        $response = new JsonResponse();
        $response->setStatusCode($srv->httpCode());
        $response->setData($output);

        return $response;
    }
}
