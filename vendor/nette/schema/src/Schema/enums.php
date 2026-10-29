<?php declare(strict_types=1);

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

namespace Nette\Schema;


/**
 * The category of value a schema element accepts, the 'kind' of describe().
 * @internal
 */
enum Kind
{
	case Any;
	case Null;
	case Bool;
	case Int;
	case Float;

	/** int or float */
	case Number;
	case String;
	case Array;
	case List;
	case Iterable;
	case Structure;
	case Enum;
	case Tuple;
	case Union;
	case Instance;
	case Object;
	case Callable;
	case Other;
}


/**
 * Defines the strategy for merging array values from multiple configuration layers.
 */
enum MergeMode
{
	case Replace;
	case OverwriteKeys;
	case AppendKeys;
}
