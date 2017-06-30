<?php namespace ALttP;

use ALttP\World;
use ALttP\Support\LocationCollection as Locations;
use Log;

abstract class Filler {
	protected $world;

	/**
	 * Returns a Filler of a specified type.
	 *
	 * @param string|null $type type of Filler requested
	 *
	 * @return self
	 */
	public static function factory($type = null, World $world = null) : self {
		if (!$world) {
			$world = new World;
		}

		switch ($type) {
			case 'Troll':
				return new Filler\Troll($world);
			case 'Distributed':
				return new Filler\Distributed($world);
			case 'Beatable':
				return new Filler\RandomBeatable($world);
			case 'RandomAssumed':
				return new Filler\RandomAssumed($world);
			case 'Random':
			default:
				return new Filler\RandomSwap($world);
		}
	}

	public function __construct(World $world) {
		$this->world = $world;
	}

	abstract public function fill(array $required, array $nice, array $extra);

	protected function shuffleLocations(Locations $locations) {
		return $locations->randomCollection($locations->count());
	}

	protected function shuffleItems(array $items) {
		return mt_shuffle($items);
	}

	protected function fastFillItemsInLocations($fill_items, $locations) {
		foreach($locations as $location) {
			if ($location->hasItem()) {
				continue;
			}
			$item = array_pop($fill_items);
			if (!$item) {
				break;
			}
			Log::debug(sprintf('Placing: %s in %s', $item->getNiceName(), $location->getName()));
			$location->setItem($item);
		}
	}
}
