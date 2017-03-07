<?php

namespace App\Model;

use Doctrine;
use Kdyby;
use Pd;
use App;


final class ClassMetadataFactory extends Kdyby\Doctrine\Mapping\ClassMetadataFactory
{

	/** @var  Kdyby\Doctrine\EntityManager */
	private $em;


	public function setEntityManager(Doctrine\ORM\EntityManagerInterface $em)
	{
		parent::setEntityManager($em);
		$this->em = $em;
	}


	public function getMetadataFor($className)
	{
		$result = parent::getMetadataFor($className);
		return $result;
	}


	protected function loadMetadata($name)
	{
		$result = parent::loadMetadata($name);
		return $result;
	}


	protected function getParentClasses($name)
	{
		$extending = [
//			Pd\Orders\Order::class,
//			Pd\Product\Warehouse::class,
//			Pd\Orders\Items\ProductItem::class,
		];

		$parents = parent::getParentClasses($name);

		return array_filter($parents, function ($item) use ($extending) {
			if (in_array($item, $extending)) {
				return FALSE;
			}

			return TRUE;
		});
	}

}
