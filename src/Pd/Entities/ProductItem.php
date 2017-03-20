<?php

namespace Pd\Entities;

use Doctrine;
use Doctrine\ORM\Mapping as ORM;
use Nette;


/**
 * Produktový item v objednávce
 *
 * @ORM\Entity
 */
class ProductItem extends Item
{

	public function __construct($name, $quantity)
	{
		parent::__construct();
		$this->name = $name;
		$this->quantity = $quantity;

	}

}
