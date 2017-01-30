<?php

declare(strict_types = 1);

namespace App\Presenters;

use Nette;
use App\Model;
use Kdyby;


class TestPresenter extends BasePresenter
{

	/** @var Kdyby\Doctrine\EntityManager @inject */
	public $em;


	public function renderDefault()
	{
		$this->template->anyVariable = 'any value';
	}


	public function actionDefault()
	{
		$this->template->products = $this->em->getRepository(\App\Entities\Product\IProduct::class)->findAll();
	}

}
