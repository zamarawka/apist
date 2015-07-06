<?php

use SleepingOwl\Apist\ApistConf;
use SleepingOwl\Apist\Apist;

class TestApi extends Apist
{

	public function index()
	{
		return $this->get('/', [
			'title'     => ApistConf::filter('.page_head .title'),
			'copyright' => ApistConf::filter('.copyright .about a')->first()->attr('href'),
			'posts'     => ApistConf::filter('.posts .post')->each(function ()
			{
				return [
					'title' => ApistConf::filter('h1.title a')->text()
				];
			}),
		]);
	}

	public function element_not_found()
	{
		return $this->get('/', [
			'title' => ApistConf::filter('.page_header')
		]);
	}

	public function non_array_blueprint()
	{
		return $this->get('/', ApistConf::filter('.page_head .title'));
	}

} 