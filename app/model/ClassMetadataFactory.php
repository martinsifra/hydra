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

	/** @var Kdyby\Doctrine\Configuration */
	private $config;


	public function setEntityManager(Doctrine\ORM\EntityManagerInterface $em)
	{
		$this->em = $em;
		$this->config = $em->getConfiguration();
		parent::setEntityManager($em);
	}


	/**
	 * @param Kdyby\Doctrine\Mapping\ClassMetadata $class
	 * @param Kdyby\Doctrine\Mapping\ClassMetadata|null $parent
	 * @param bool $rootEntityFound
	 * @param array $nonSuperclassParents
	 */
	protected function doLoadMetadata($class, $parent, $rootEntityFound, array $nonSuperclassParents)
	{
		parent::doLoadMetadata($class, $parent, $rootEntityFound, $nonSuperclassParents);

		if ($parent && $this->isOverridden($parent->getName())) {
			$class->setPrimaryTable($parent->table);
			$class->setDiscriminatorMap(array_merge($parent->discriminatorMap, $class->discriminatorMap));
			$class->setDiscriminatorColumn($parent->discriminatorColumn);
			$class->setSubclasses($parent->subClasses + $class->subClasses);

			array_walk($class->fieldMappings, function (&$mapping, $field) use ($class) {
				if ($class->isInheritanceTypeNone()) {
					if (isset($mapping['inherited'])) {
						unset($mapping['inherited']);
					}
				} elseif ($class->isInheritanceTypeSingleTable()) {
					if (isset($mapping['inherited'])) {
						unset($mapping['inherited']);
					}
				}
			});

			array_walk($class->associationMappings, function (&$mapping, $field) use ($class) {
				if ($class->isInheritanceTypeNone()) {
					if (isset($mapping['inherited'])) {
						unset($mapping['inherited']);
					}
				} elseif ($class->isInheritanceTypeSingleTable()) {
					if (isset($mapping['inherited'])) {
						unset($mapping['inherited']);
					}
				}
			});
		}
	}


	protected function isEntity(Doctrine\Common\Persistence\Mapping\ClassMetadata $class)
	{
		if ($this->isOverridden($class->getName())) {
			return FALSE;
		}

		return parent::isEntity($class);
	}


	protected function isOverridden($className): bool
	{
		if ($this->config instanceof Kdyby\Doctrine\Configuration) {
			$alias = $this->config->getTargetEntityClassName($className);

			if ($alias !== $className) {
				return TRUE;
			}
		}

		return FALSE;
	}

	//	protected function getParentClasses($name)
	//	{
	//		$extending = [
	//			Pd\Entities\Order::class,
	//			Pd\Product\Warehouse::class,
	//			Pd\Orders\Items\ProductItem::class,
	//		];
	//
	//		return $parents = parent::getParentClasses($name);
	//
	//		return array_filter($parents, function ($item) use ($extending) {
	//			if (in_array($item, $extending)) {
	//				return FALSE;
	//			}
	//
	//			return TRUE;
	//		});
	//	}

}
