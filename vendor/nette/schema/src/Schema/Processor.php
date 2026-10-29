<?php declare(strict_types=1);

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

namespace Nette\Schema;

use Nette;


/**
 * Schema validator.
 */
final class Processor
{
	/** @var list<\Closure(Context): void> */
	public array $onNewContext = [];
	private Context $context;
	private bool $skipDefaults = false;


	/**
	 * When enabled, missing properties are not filled with their default values.
	 */
	public function skipDefaults(bool $value = true): void
	{
		$this->skipDefaults = $value;
	}


	/**
	 * Normalizes and validates data. Result is a clean completed data.
	 * @throws ValidationException
	 */
	public function process(Schema $schema, mixed $data): mixed
	{
		$context = $this->createContext();
		try {
			$data = $schema->normalize($data, $context);
			$this->throwErrors($context);
			$data = $schema->complete($data, $context);
			$this->throwErrors($context);
			return $data;
		} finally {
			$this->context = $context;
		}
	}


	/**
	 * Normalizes and validates and merges multiple data. Result is a clean completed data.
	 * @param  list<mixed>  $dataset
	 * @throws ValidationException
	 */
	public function processMultiple(Schema $schema, array $dataset): mixed
	{
		$context = $this->createContext();
		try {
			$flatten = null;
			$first = true;
			foreach ($dataset as $data) {
				$data = $schema->normalize($data, $context);
				$this->throwErrors($context);
				$flatten = $first ? $data : $schema->merge($data, $flatten);
				$first = false;
			}

			$data = $schema->complete($flatten, $context);
			$this->throwErrors($context);
			return $data;
		} finally {
			$this->context = $context;
		}
	}


	/**
	 * Returns all deprecation warnings collected during the last processing run.
	 * @return list<string>
	 */
	public function getWarnings(): array
	{
		$res = [];
		if (isset($this->context)) {
			foreach ($this->context->warnings as $message) {
				$res[] = $message->toString();
			}
		}

		return $res;
	}


	private function throwErrors(Context $context): void
	{
		if ($context->errors) {
			throw new ValidationException(null, $context->errors);
		}
	}


	private function createContext(): Context
	{
		$context = new Context;
		$context->skipDefaults = $this->skipDefaults;
		Nette\Utils\Arrays::invoke($this->onNewContext, $context);
		return $context;
	}
}
