<?php

namespace Pd\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Kdyby;


/**
 * @ORM\Entity
 * @ORM\Table("orders")
 */
class Order
{

	use Kdyby\Doctrine\Entities\MagicAccessors;
	use Kdyby\Doctrine\Entities\Attributes\Identifier;


	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	protected $email;

	/**
	 * @ORM\OneToMany(targetEntity=Pd\Entities\Item::class, mappedBy="order", cascade={"persist"}, indexBy="id")
	 * @var Item[]|Collection
	 */
	protected $items;


	public function __construct()
	{
		$this->items = new ArrayCollection;
	}

}
