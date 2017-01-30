<?php

namespace App\Entities\Product;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\MappedSuperclass
 */
class Base implements IProduct
{

	use \Kdyby\Doctrine\Entities\Attributes\Identifier;
	use \Kdyby\Doctrine\Entities\MagicAccessors;

	/**
	 * @ORM\OneToOne(targetEntity="\App\Entities\Product\IProduct")
	 * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
	 */
	protected $similar;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $name;

}
