<?php

namespace rdx\pathethuis;

class Movie extends Model {

	static $_table = 'movies';

	protected function get_full_url() {
		return sprintf(PATHE_URL, $this->pathe_id, 'x');
	}

	protected function relate_last_price() {
		return $this->to_first(PriceChange::class, 'movie_id')
			->where('id in (select max(id) from price_changes group by movie_id)');
	}

}
