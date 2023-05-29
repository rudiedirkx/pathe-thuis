<?php

namespace rdx\pathethuis;

use InvalidArgumentException;
use rdx\jsdom\Node;

class Movie extends Model {

	const MIN_ITEMS_IN_HTML = 2;

	static $_table = 'movies';

	protected function get_full_url() {
		return sprintf(PATHE_URL, $this->pathe_id, 'x');
	}

	protected function get_imdb_url() {
		return sprintf('https://www.imdb.com/title/%s/', $this->imdb_id);
	}

	protected function relate_prices() {
		return $this->to_many(PriceChange::class, 'movie_id')
			->order('id desc');
	}

	protected function relate_last_price() {
		return $this->to_first(PriceChange::class, 'movie_id')
			->where('id in (select max(id) from price_changes group by movie_id)');
	}

	static public function htmlToMovies(string $html) : array {
		$doc = Node::create($_POST['html']);
		$elements = $doc->queryAll('.vertical-poster-list__item');

		if (($n = count($elements)) < self::MIN_ITEMS_IN_HTML) {
			throw new InvalidArgumentException("$n items in source");
		}

		$items = [];
		foreach ($elements as $element) {
			$href = $element->query('a')['href'];
			preg_match('#^/film/(\d+)/#', $href, $match);
			$id = $match[1];
			$name = $element->query('.poster__caption')->textContent;
			$items[] = [$id, $name];
		}

		return $items;
	}

	static public function syncMovies(array $items) : array {
		$movies = self::all("deleted = '0' ORDER BY name");
		$exist = array_column($movies, 'name', 'pathe_id');

		return self::$_db->transaction(function() use ($exist, $items) {
			$created = 0;
			foreach ($items as [$id, $name]) {
				if (!isset($exist[$id])) {
					$created++;
					self::insert([
						'name' => $name,
						'pathe_id' => $id,
					]);
				}
				else {
					unset($exist[$id]);
				}
			}

			if ($deleted = count($exist)) {
				self::updateAll([
					'deleted' => 1,
				], [
					'pathe_id' => array_keys($exist),
				]);
			}
// print_r(self::$_db->queries);
// exit;

			return [$created, $deleted];
		});
	}

}
