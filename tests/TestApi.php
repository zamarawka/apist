<?php

use SleepingOwl\Apist\ApistConf;
use SleepingOwl\Apist\Apist;

class TestApi extends Apist
{

	public function index()
	{
		return $this->get('/', [
			'title'     => ApistConf::select('.page_head .title'),
			'copyright' => ApistConf::select('.copyright .about a')->first()->attr('href'),
			'posts'     => ApistConf::select('.posts .post')->each([
                'title' => ApistConf::select('h1.title a')->text()
            ]),
		]);
	}

	public function element_not_found()
	{
		return $this->get('/', [
			'title' => ApistConf::select('.page_header')
		]);
	}

	public function non_array_blueprint()
	{
		return $this->get('/', ApistConf::select('.page_head .title'));
	}

} 