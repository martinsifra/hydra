<?php

namespace App\Entities\Product;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 */
class Benu extends Base
{

	/**
	 * @ORM\Column(type="string")
	 */
	protected $benu;
}
