<?php

/**
 * This file is part of the NasExt extensions of Nette Framework
 *
 * Copyright (c) 2013 Dusan Hudak (http://dusan-hudak.com)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace NasExt\Forms;

use Nette;


/**
 * @author Dusan Hudak
 * @author Ales Wita
 * @license MIT
 */
class DependentData
{
	/** @var array */
	private $items = [];

	/** @var string|int */
	private $value;

	/** @var string */
	private $prompt;


	/**
	 * @param array
	 * @param string|int
	 * @param string
	 */
	public function __construct(array $items = [], $value = null, $prompt = null)
	{
		$this->items = $items;
		$this->value = $value;
		$this->prompt = $prompt;
	}


	/**
	 * @param array
	 * @return self
	 */
	public function setItems(array $items)
	{
		$this->items = $items;
		return $this;
	}


	/**
	 * @param string|int
	 * @return self
	 */
	public function setValue($value)
	{
		$this->value = $value;
		return $this;
	}


	/**
	 * @param string|int
	 * @return self
	 */
	public function setPrompt($value)
	{
		$this->prompt = $value;
		return $this;
	}


	/**
	 * @return array
	 */
	public function getItems()
	{
		return $this->items;
	}


	/**
	 * @param array
	 * @return array
	 */
	public function getPreparedItems($disabledItems = null)
	{
		$items = [];
		foreach ($this->items as $key => $item) {
			if (!($item instanceof Nette\Utils\Html)) {
				$el = Nette\Utils\Html::el('option')->value($key)->setText($item);
			} else {
				$el = $item;
			}

			// disable element
			if (is_array($disabledItems) && array_key_exists($key, $disabledItems) && $disabledItems[$key] === true) {
				$el->disabled(true);
			}

			$items[$key] = [
				'key' => $el->getValue(),
				'value' => $el->getText(),
			];

			end($items);
			$lKey = key($items);
			foreach ($el->attrs as $attr => $val) {
				$items[$lKey]['attributes'][$attr] = $val;
			}
		}

		return $items;
	}


	/**
	 * @return string|int
	 */
	public function getValue()
	{
		return $this->value;
	}


	/**
	 * @return string
	 */
	public function getPrompt()
	{
		return $this->prompt;
	}
}