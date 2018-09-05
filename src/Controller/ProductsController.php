<?php

namespace App\Controller;

use App\Entity\Product;
use App\Service\Products;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ProductsController extends AbstractController
{
    /**
     * @Route("/products", name="products")
     */
    public function index()
    {
        return $this->render('products/index.html.twig', [
            'controller_name' => 'ProductsController',
        ]);
    }

	/**
	 * @Route("/products/{id}", name="products_show", requirements={"id"="\d+"})
	 * @ParamConverter("product", options={"mapping"={"id"="id"}})
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
    public function show(Product $product)
	{
		return $this->render('products/show.html.twig', [
			'product' => $product
		]);
	}

}
