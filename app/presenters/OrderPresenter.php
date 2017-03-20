<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use App\Model;


class OrderPresenter extends BasePresenter
{

	/** @var \Kdyby\Doctrine\EntityManager @inject */
	public $em;

	public function renderDefault()
	{
		$orders = $this->em->getRepository(\Pd\Entities\Order::class)->findAll();

		$this->getTemplate()->add('orders', $orders);


		bdump($this->em->getClassMetadata(\App\Entities\Item::class));
	}

}
