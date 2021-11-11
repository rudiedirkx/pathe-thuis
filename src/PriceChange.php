<?php

namespace rdx\pathethuis;

class PriceChange extends Model {

	static $_table = 'price_changes';

	public function init() {
		$this->price = round($this->price, 2);
	}

}
