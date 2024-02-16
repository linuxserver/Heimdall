<?php

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

declare(strict_types=1);

namespace Nette\Schema;

use Nette;
use Nette\Schema\Elements\AnyOf;
use Nette\Schema\Elements\Structure;
use Nette\Schema\Elements\Type;


/**
 * Schema generator.
 *
 * @method static Type scalar($default = null)
 * @method static Type string($default = null)
 * @method static Type int($default = null)
 * @method static Type float($default = null)
 * @method static Type bool($default = null)
 * @method static Type null()
 * @method static Type array($default = [])
 * @method static Type list($default = [])
 * @method static Type mixed($default = null)
 * @method static Type email($default = null)
 * @method static Type unicode($default = null)
 */
final class Expect
{
	public static function __callStatic(string $name, array $args): Type
	{
		$type = new Type($name);
		if ($args) {
			$type->default($args[0]);
		}

		return $type;
	}


	public static function type(string $type): Type
	{
		return new Type($type);
	}


	public static function anyOf(mixed ...$set): AnyOf
	{
		return new AnyOf(...$set);
	}


	/**
	 * @param  Schema[]  $items
	 */
	public static function structure(array $items): Structure
	{
		return new Structure($items);
	}


	public static function from(object $object, array $items = []): Structure
	{
		$ro = new \ReflectionObject($object);
		$props = $ro->hasMethod('__construct')
			? $ro->getMethod('__construct')->getParameters()
			: $ro->getProperties();

		foreach ($props as $prop) {
			$item = &$items[$prop->getName()];
			if (!$item) {
				$type = Helpers::getPropertyType($prop) ?? 'mixed';
				$item = new Type($type);
				if ($prop instanceof \ReflectionProperty ? $prop->isInitialized($object) : $prop->isOptional()) {
					$def = ($prop instanceof \ReflectionProperty ? $prop->getValue($object) : $prop->getDefaultValue());
					if (is_object($def)) {
						$item = static::from($def);
					} elseif ($def === null && !Nette\Utils\Validators::is(null, $type)) {
						$item->required();
					} else {
						$item->default($def);
					}
				} else {
					$item->required();
				}
			}
		}

		return (new Structure($items))->castTo($ro->getName());
	}


	public static function arrayOf(string|Schema $valueType, string|Schema $keyType = null): Type
	{
		return (new Type('array'))->items($valueType, $keyType);
	}


	public static function listOf(string|Schema $type): Type
	{
		return (new Type('list'))->items($type);
	}
}
