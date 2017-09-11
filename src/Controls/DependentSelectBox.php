<?php

/**
 * This file is part of the NasExt extensions of Nette Framework
 *
 * Copyright (c) 2013 Dusan Hudak (http://dusan-hudak.com)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace NasExt\Forms\Controls;

use NasExt;
use Nette;


/**
 * @author Jáchym Toušek
 * @author Dusan Hudak
 * @author Ales Wita
 * @license MIT
 */
class DependentSelectBox extends Nette\Forms\Controls\SelectBox implements Nette\Application\UI\ISignalReceiver
{
	use NasExt\Forms\DependentTrait;

	/** @var string */
	const SIGNAL_NAME = 'load';


	/**
	 * @param string
	 * @param array[Nette\Forms\IControl]
	 */
	public function __construct($label, array $parents)
	{
		$this->parents = $parents;
		parent::__construct($label);
	}


	/**
	 * @throws Nette\InvalidStateException
	 * @param string
	 * @return void
	 */
	public function signalReceived($signal)
	{
		$presenter = $this->lookup('Nette\\Application\\UI\\Presenter');

		if ($presenter->isAjax() && $signal === self::SIGNAL_NAME && !$this->isDisabled()) {
			$parentsNames = [];
			foreach ($this->parents as $parent) {
				$parentsNames[$parent->getName()] = $presenter->getParameter($parent->getName());
			}

			$data = $this->getDependentData([$parentsNames]);
			$presenter->payload->dependentselectbox = [
				'id' => $this->getHtmlId(),
				'items' => $data->getPreparedItems(!is_array($this->disabled) ?: $this->disabled),
				'value' => $data->getValue(),
				'prompt' => $data->getPrompt() === null ? $this->translate($this->getPrompt()) : $this->translate($data->getPrompt()),
				'disabledWhenEmpty' => $this->disabledWhenEmpty,
			];
			$presenter->sendPayload();
		}
	}


	/**
	 * @return void
	 */
	private function tryLoadItems()
	{
		if ($this->parents === array_filter($this->parents, function ($p) {return !$p->hasErrors();})) {
			$parentsValues = [];
			foreach ($this->parents as $parent) {
				$parentsValues[$parent->getName()] = $parent->getValue();
			}

			$data = $this->getDependentData([$parentsValues]);
			$items = $data->getItems();


			if ($this->getForm()->isSubmitted()) {
				$this->setValue($this->value);

			} elseif ($this->tempValue !== null) {
				$this->setValue($this->tempValue);

			} else {
				$this->setValue($data->getValue());
			}


			if (count($items) > 0) {
				$this->loadHttpData();

				$this->setItems($items)
					->setPrompt($data->getPrompt() === null ? $this->getPrompt() : $data->getPrompt());

				if ($this->disabledWhenEmpty === true && $this->disabled !== true) {
					$this->setDisabled(false);
					$this->setOmitted(false);
				}

			} else {
				if ($this->disabledWhenEmpty === true) {
					$this->setDisabled();
				}
			}
		}
	}
}
