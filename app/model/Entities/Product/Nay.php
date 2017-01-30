<?php

namespace App\Entities\Product;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 */
class Nay extends Base
{

	/**
	 * @ORM\Column(type="string")
	 */
	protected $nay;
}
