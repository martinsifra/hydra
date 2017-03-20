<?php

namespace App\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Kdyby;
use Pd;


/**
 * @ORM\Entity
 */
class Order extends Pd\Entities\Order
{

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	protected $overridden;

}
