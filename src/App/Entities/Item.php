<?php

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;
use Kdyby;


/**
 * @ORM\Entity
 * @ORM\Table("orders_items")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorMap({
 *   "frigo"   = "App\Entities\Refrigerator"
 * })
 */
abstract class Item extends \Pd\Entities\Item
{

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 * @var integer
	 */
	protected $id;


	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
//	protected $appFeature;
}
