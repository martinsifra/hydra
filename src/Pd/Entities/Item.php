<?php

namespace Pd\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Nette;
use Kdyby;


/**
 * Spolecny zaklad pro polozky v objednavce
 *
 * @ORM\Entity
 * @ORM\Table("orders_items")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="itemType", type="string")
 * @ORM\DiscriminatorMap({
 *   "product"   = "Pd\Entities\ProductItem"
 * })
 */
abstract class Item
{

//	use Kdyby\Doctrine\Entities\Attributes\Identifier;
	use Kdyby\Doctrine\Entities\MagicAccessors;


	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 * @var integer
	 */
	protected $id;

	/**
	 * @ORM\ManyToOne(targetEntity=Pd\Entities\Order::class, inversedBy="items", cascade={"persist"})
	 * @ORM\JoinColumn(name="order_id", referencedColumnName="id")
	 * @var Order
	 */
	protected $order;

	/**
	 * @ORM\ManyToOne(targetEntity=Pd\Entities\Item::class, inversedBy="childItems", cascade={"persist"})
	 * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
	 * @var Item
	 */
	protected $parentItem;

	/**
	 * @ORM\OneToMany(targetEntity=Pd\Entities\Item::class, mappedBy="parentItem", cascade={"persist"})
	 * @var Item[]
	 */
	protected $childItems;

	/**
	 * id skupiny pro grupování položek (např. položky výhodného balíčku)
	 *
	 * Může být jakýkoliv string a pak všechny položky se stejnou hodnotou patří do stejné skupiny.
	 * Pokud je null, nepatří do žádné skupiny.
	 *
	 * @ORM\Column(type="string", nullable=TRUE)
	 * @var string
	 */
	protected $groupId;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	protected $name;

	/**
	 * @ORM\Column(type="float")
	 * @var float
	 */
	protected $quantity;


	public function __construct()
	{
		$this->childItems = new ArrayCollection();
	}

}
