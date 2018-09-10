<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class Products
{

	/**
	 * @var EntityManager
	 */
	private $em;

	/**
	 * @var EntityRepository
	 */
	private $repo;

	/**
	 * @var EntityRepository
	 */
	private $categoriesRepo;

	public function __construct(EntityManagerInterface $em)
	{
		$this->em = $em;
		$this->repo = $this->em->getRepository(Product::class);
		$this->categoriesRepo = $this->em->getRepository(Category::class);
	}

	/**
	 * @return Product[]
	 */
	public function getAll()
	{
		return $this->repo->findAll();
	}

	/**
	 * @return Product[]
	 */
	public function getTop()
	{
		return $this->repo->findBy(['isTop' => true], ['name' => 'ASC'], 20);
	}

	public function getById($id): ?Product
	{
		return $this->repo->find($id);
	}

	public function getAllCategories()
	{
		return $this->categoriesRepo->findAll();
	}

}
