<?php

namespace App\Entities;

use Doctrine;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 */
class Refrigerator extends Item
{
	/**
	 * @ORM\Column(type="boolean")
	 * @var bool
	 */
	protected $isCold;
}
