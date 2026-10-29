# Nette Schema

[![Downloads this Month](https://img.shields.io/packagist/dm/nette/schema.svg)](https://packagist.org/packages/nette/schema)
[![Coverage Status](https://coveralls.io/repos/github/nette/schema/badge.svg?branch=master)](https://coveralls.io/github/nette/schema?branch=master)
[![Latest Stable Version](https://poser.pugx.org/nette/schema/v/stable)](https://github.com/nette/schema/releases)
[![License](https://img.shields.io/badge/license-New%20BSD-blue.svg)](https://github.com/nette/schema/blob/master/license.md)


Introduction
============

A practical library for validating and normalizing data structures against a given schema with a smart, easy-to-understand API.

Documentation can be found on the [website](https://doc.nette.org/schema).

Installation:

```shell
composer require nette/schema
```

It requires PHP version 8.1 and supports PHP up to 8.6.


[Support Me](https://github.com/sponsors/dg)
--------------------------------------------

Do you like Nette Schema? Are you looking forward to the new features?

[![Buy me a coffee](https://files.nette.org/icons/donation-3.svg)](https://github.com/sponsors/dg)

Thank you!


Basic Usage
-----------

In the variable `$schema`, we have a validation schema (we'll explain what this means and how to create one in a moment), and in the variable `$data`, we have the data structure we want to validate and normalize. This could be, for example, data submitted by a user via an API, a configuration file, etc.

The task is handled by the [Nette\Schema\Processor](https://api.nette.org/schema/master/Nette/Schema/Processor.html) class, which processes the input and either returns normalized data or throws a [Nette\Schema\ValidationException](https://api.nette.org/schema/master/Nette/Schema/ValidationException.html) exception if an error occurs.

```php
$processor = new Nette\Schema\Processor;

try {
	$normalized = $processor->process($schema, $data);
} catch (Nette\Schema\ValidationException $e) {
	echo 'Data is invalid: ' . $e->getMessage();
}
```

The method `$e->getMessages()` returns an array of all messages as strings, and `$e->getMessageObjects()` returns all messages as [Nette\Schema\Message](https://api.nette.org/schema/master/Nette/Schema/Message.html) objects.


Defining the Schema
-------------------

And now let's create the schema. The class [Nette\Schema\Expect](https://api.nette.org/schema/master/Nette/Schema/Expect.html) is used to define it; we essentially define expectations for what the data should look like. Let's say the input data must be a structure (e.g., an array) containing elements `processRefund` of type bool and `refundAmount` of type int.

```php
use Nette\Schema\Expect;

$schema = Expect::structure([
	'processRefund' => Expect::bool(),
	'refundAmount' => Expect::int(),
]);
```

We believe the schema definition looks understandable, even if you're seeing it for the first time.

Let's send the following data for validation:

```php
$data = [
	'processRefund' => true,
	'refundAmount' => 17,
];

$normalized = $processor->process($schema, $data); // OK, passes validation
```

The output, i.e., the value `$normalized`, is a `stdClass` object. If we wanted the output to be an array, we would add casting `->castTo('array')` to the schema.

All elements of the structure are optional and have a default value of `null`. Example:

```php
$data = [
	'refundAmount' => 17,
];

$normalized = $processor->process($schema, $data); // OK, passes validation
// $normalized = {'processRefund' => null, 'refundAmount' => 17}
```

The fact that the default value is `null` does not mean it would accept `'processRefund' => null` in the input data. No, the input must be a boolean, i.e. `true` or `false` only. We would have to explicitly allow `null` using `Expect::bool()->nullable()`.

An item can be made mandatory using `Expect::bool()->required()`. We can change the default value, for example, to `false` using `Expect::bool()->default(false)` or shorthand `Expect::bool(false)`.

And what if we wanted to accept `1` and `0` in addition to booleans? Then we list the values that we also want to normalize to boolean:

```php
$schema = Expect::structure([
	'processRefund' => Expect::anyOf(true, false, 1, 0)->castTo('bool'),
	'refundAmount' => Expect::int(),
]);

$normalized = $processor->process($schema, $data);
is_bool($normalized->processRefund); // true
```

Now you know the basics of defining a schema and how the structure items behave. We will now show what other elements you can use when defining a schema.


Data Types: type()
------------------

All standard PHP data types can be specified in the schema:

```php
Expect::string($default = null)
Expect::int($default = null)
Expect::float($default = null)
Expect::bool($default = null)
Expect::null()
Expect::array($default = [])
Expect::list($default = [])
```

And also all types [supported by the Validators class](https://doc.nette.org/validators#toc-expected-types), for example `Expect::type('scalar')` or shorthand `Expect::scalar()`. Also class or interface names, e.g., `Expect::type('AddressEntity')`.

Union syntax can also be used:

```php
Expect::type('bool|string|array')
```

The default value is always `null` with the exception of `array` and `list`, where it is an empty array. (A list is an array indexed by a sequence of numeric keys starting from zero, i.e. a non-associative array).


Array of Values: arrayOf() listOf()
-----------------------------------

An array represents too general a structure; it's more useful to specify precisely which elements it may contain. For example, an array whose elements can only be strings:

```php
$schema = Expect::arrayOf('string');

$processor->process($schema, ['hello', 'world']); // OK
$processor->process($schema, ['a' => 'hello', 'b' => 'world']); // OK
$processor->process($schema, ['key' => 123]); // ERROR: 123 is not a string
```

The second parameter can specify keys (since version 1.2):

```php
$schema = Expect::arrayOf('string', 'int');

$processor->process($schema, ['hello', 'world']); // OK
$processor->process($schema, ['a' => 'hello']); // ERROR: 'a' is not an int
```

A list is an indexed array:

```php
$schema = Expect::listOf('string');

$processor->process($schema, ['a', 'b']); // OK
$processor->process($schema, ['a', 123]); // ERROR: 123 is not a string
$processor->process($schema, ['key' => 'a']); // ERROR: not a list
$processor->process($schema, [1 => 'a', 0 => 'b']); // ERROR: also not a list
```

With the `wrap` parameter, a single value is also accepted and wrapped into a one-item list (since version 1.4):

```php
$schema = Expect::listOf('string', wrap: true);

$processor->process($schema, 'hello'); // OK, gives ['hello']
$processor->process($schema, ['a', 'b']); // OK
```

The parameter can also be a schema, so we can write:

```php
Expect::arrayOf(Expect::bool())
```

The default value is an empty array. If you specify a default value, it will be merged with the passed data. This can be disabled using `mergeDefaults(false)` (since version 1.1).


Enumeration: anyOf()
--------------------

`anyOf()` represents a set of values or schemas that a value can take. Here's how to write an array of elements that can be either `'a'`, `true`, or `null`:

```php
$schema = Expect::listOf(
	Expect::anyOf('a', true, null),
);

$processor->process($schema, ['a', true, null, 'a']); // OK
$processor->process($schema, ['a', false]); // ERROR: false does not belong there
```

The elements of the enumeration can also be schemas:

```php
$schema = Expect::listOf(
	Expect::anyOf(Expect::string(), true, null),
);

$processor->process($schema, ['foo', true, null, 'bar']); // OK
$processor->process($schema, [123]); // ERROR
```

The `anyOf()` method accepts variants as separate parameters, not as an array. To pass it an array of values, use the unpack operator `anyOf(...$variants)`.

Variants are tried in the order they are written and the first matching one wins. The single exception is `null`: when it belongs to the accepted values, a `null` input always gives `null`, even though a structure or an array variant would otherwise read it as an empty value of its own.

The default value is `null`. Use the `firstIsDefault()` method to make the first item the default:

```php
// default is 'hello'
Expect::anyOf(Expect::string('hello'), true, null)->firstIsDefault();
```


Enums: enum()
-------------

A configuration file can only carry a plain string like `'hearts'`, but in your code you would much rather work with an enum. That is the gap `enum()` closes (since version 1.4): give it an enum with a backing value (`enum Suit: string`) and it accepts either the backing value or the case itself, always handing you back the case:

```php
enum Suit: string
{
	case Hearts = 'hearts';
	case Spades = 'spades';
}

$schema = Expect::enum(Suit::class);

$processor->process($schema, 'hearts');     // OK, returns Suit::Hearts
$processor->process($schema, Suit::Spades); // OK, returns Suit::Spades
$processor->process($schema, 'clubs');      // ERROR: expects to be Suit
```

Enums backed by integers work exactly the same, and so does everything you know from the other elements, such as `Expect::enum(Suit::class)->required()` or `->default(Suit::Hearts)`.

When [generating a schema from a class](#mapping-to-objects-from), a `public Suit $suit` property gives you this very element, so you do not have to reach for `enum()` there. `Expect::type(Suit::class)` is a different thing: like any class name, it accepts only an instance, i.e. the case itself.


Structures
----------

Structures are objects with defined keys. Each key-value pair is referred to as a "property".

Structures accept arrays and objects and return `stdClass` objects.

By default, all properties are optional and have a default value of `null`. You can define mandatory properties using `required()`:

```php
$schema = Expect::structure([
	'required' => Expect::string()->required(),
	'optional' => Expect::string(), // default value is null
]);

$processor->process($schema, ['optional' => '']);
// ERROR: the mandatory item 'required' is missing

$processor->process($schema, ['required' => 'foo']);
// OK, returns {'required' => 'foo', 'optional' => null}
```

A structure itself is mandatory. Therefore, if it is nested inside another structure and the input does not contain it, it is created anyway - and it reports an error when it contains a required property. Use `required(false)` to make the entire nested structure optional. If it is missing in the input, `null` appears in the output, but if it is present, its required properties are enforced:

```php
$schema = Expect::structure([
	'db' => Expect::structure([
		'dsn' => Expect::string()->required(),
	])->required(false),
]);

$processor->process($schema, []);
// OK, returns {'db' => null}

$processor->process($schema, ['db' => []]);
// ERROR: 'db › dsn' is missing
```

If you do not want properties with default values in the output, use `skipDefaults()`:

```php
$schema = Expect::structure([
	'required' => Expect::string()->required(),
	'optional' => Expect::string(),
])->skipDefaults();

$processor->process($schema, ['required' => 'foo']);
// OK, returns {'required' => 'foo'}
```

Although `null` is the default value for the `optional` property, it is not allowed in input data (the value must be a string). Properties accepting `null` are defined using `nullable()`:

```php
$schema = Expect::structure([
	'optional' => Expect::string(),
	'nullable' => Expect::string()->nullable(),
]);

$processor->process($schema, ['optional' => null]);
// ERROR: 'optional' expects to be string, null given.

$processor->process($schema, ['nullable' => null]);
// OK, returns {'optional' => null, 'nullable' => null}
```

The array of all structure properties is returned by the `getShape()` method.

By default, no additional items can be present in the input data:

```php
$schema = Expect::structure([
	'key' => Expect::string(),
]);

$processor->process($schema, ['additional' => 1]);
// ERROR: Unexpected item 'additional'
```

This can be changed using `otherItems()`. As a parameter, pass the schema to validate each extra item:

```php
$schema = Expect::structure([
	'key' => Expect::string(),
])->otherItems(Expect::int());

$processor->process($schema, ['additional' => 1]); // OK
$processor->process($schema, ['additional' => true]); // ERROR
```

You can create a new structure by extending another using `extend()`:

```php
$dog = Expect::structure([
	'name' => Expect::string(),
	'age' => Expect::int(),
]);

$dogWithBreed = $dog->extend([
	'breed' => Expect::string(),
]);
```


Array
-----

An array with defined keys (since version 1.3.2). Everything that applies to [structures](#structures) applies to it.

```php
$schema = Expect::array([
	'required' => Expect::string()->required(),
	'optional' => Expect::string(), // default value is null
]);
```

Tuples: tuple()
---------------

A tuple is a fixed-size array where each position has its own schema (since version 1.4):

```php
$schema = Expect::tuple([
	Expect::int(),
	Expect::string(),
	Expect::bool(),
]);

$processor->process($schema, [1, 'hello', true]); // OK
```

The input must be a list, positions given by keys or in another order are refused. When configurations are layered, a later tuple replaces the earlier one wholesale - mixing positions of two tuples is never what you meant. Positions beyond the shape can be allowed with `otherItems()`, which describes the rest elements, and `extend()` appends further positions.

Unlike a structure, a tuple does not accept `null` as an empty value; call `nullable()` when `null` is a valid value.


Deprecated Properties
---------------------

You can mark a property as deprecated using the `deprecated([string $message])` method. Information about deprecation is returned using `$processor->getWarnings()`:

```php
$schema = Expect::structure([
	'old' => Expect::int()->deprecated('The item %path% is deprecated'),
]);

$processor->process($schema, ['old' => 1]); // OK
$processor->getWarnings(); // ["The item 'old' is deprecated"]
```


Description: description()
--------------------------

Any element can carry a human-readable description (since version 1.4). It has no effect on validation - the schema is often the only place where the meaning of a configuration option is written down at all, and `description()` is where you write it. It also travels into the [JSON Schema export](#exporting-to-json-schema):

```php
$schema = Expect::structure([
	'host' => Expect::string('localhost')->description('Database server hostname'),
	'port' => Expect::int(3306)->description('Port the server listens on'),
])->description('Database connection');
```


Ranges: min() max()
-------------------

Use `min()` and `max()` to limit the count for arrays:

```php
// array, at least 10 items, maximum 20 items
Expect::array()->min(10)->max(20);
```

For strings, they limit the length:

```php
// string, at least 10 bytes long, maximum 20 bytes
Expect::string()->min(10)->max(20);
```

The length of a string is measured in bytes. To count characters of a UTF-8 string, use `Expect::unicode()`.

For numbers, they limit the value:

```php
// integer, between 10 and 20 inclusive
Expect::int()->min(10)->max(20);
```

Of course, it is possible to specify just `min()` or just `max()`:

```php
// string, maximum 20 bytes
Expect::string()->max(20);
```


Regular Expressions: pattern()
------------------------------

Using `pattern()`, you can specify a regular expression that the **entire** input string must match (i.e. as if it were wrapped in `^` and `$` characters):

```php
// exactly 9 digits
Expect::string()->pattern('\d{9}');
```

A pattern belongs to strings, just like `items()` belongs to arrays. Attach either of them to an element of a different kind and Schema raises a deprecation notice instead of silently ignoring the constraint (since version 1.4).


Custom Assertions: assert()
---------------------------

You can add any other constraints using `assert(callable $fn)`.

```php
$countIsEven = fn($v) => count($v) % 2 === 0;

$schema = Expect::arrayOf('string')
	->assert($countIsEven); // the count must be even

$processor->process($schema, ['a', 'b']); // OK
$processor->process($schema, ['a', 'b', 'c']); // ERROR: 3 is not an even count
```

Or

```php
Expect::string()->assert('is_file'); // file must exist
```

You can add a custom description to each assertion. It will be part of the error message.

```php
$schema = Expect::arrayOf('string')
	->assert($countIsEven, 'Even items in array');

$processor->process($schema, ['a', 'b', 'c']);
// Failed assertion "Even items in array" for item with value array.
```

The method can be called repeatedly to add multiple constraints. It can be interleaved with calls to `transform()` and `castTo()`.


Transformation: transform()
---------------------------

Successfully validated data can be modified using a custom function:

```php
// convert to uppercase:
Expect::string()->transform(fn(string $s) => strtoupper($s));
```

The method can be called repeatedly to add multiple transformations. It can be interleaved with calls to `assert()` and `castTo()`. The operations are performed in the order in which they are declared:

```php
Expect::type('string|int')
	->castTo('string')
	->assert('ctype_lower', 'All characters must be lowercased')
	->transform(fn(string $s) => strtoupper($s)); // convert to uppercase
```

The `transform()` method can simultaneously transform and validate the value. This is often simpler and involves less code duplication than chaining `transform()` and `assert()`. For this purpose, the function receives a [Nette\Schema\Context](https://api.nette.org/schema/master/Nette/Schema/Context.html) object with an `addError()` method, which can be used to add information about validation problems:

```php
Expect::string()
	->transform(function (string $s, Nette\Schema\Context $context) {
		if (!ctype_lower($s)) {
			$context->addError('All characters must be lowercased', 'my.case.error');
			return null;
		}

		return strtoupper($s);
	});
```


Casting: castTo()
-----------------

Successfully validated data can be cast:

```php
Expect::scalar()->castTo('string');
```

In addition to native PHP types, you can also cast to classes. It distinguishes between a simple class without a constructor and a class with a constructor. If the class has no constructor, an instance is created, and all structure elements are written to the properties:

```php
class Info
{
	public bool $processRefund;
	public int $refundAmount;
}

Expect::structure([
	'processRefund' => Expect::bool(),
	'refundAmount' => Expect::int(),
])->castTo(Info::class);

// creates '$obj = new Info' and writes to $obj->processRefund and $obj->refundAmount
```

If the class has a constructor, the structure elements are passed as named arguments to the constructor:

```php
class Info
{
	public function __construct(
		public bool $processRefund,
		public int $refundAmount,
	) {
	}
}

// creates $obj = new Info(processRefund: ..., refundAmount: ...)
```

Casting combined with a scalar parameter creates an object and passes the value as the single argument to the constructor:

```php
Expect::string()->castTo(DateTime::class);
// creates new DateTime(...)
```


Normalization: before()
-----------------------

Before the validation itself, the data can be normalized using the `before()` method. As an example, let's take an element that must be an array of strings (e.g., `['a', 'b', 'c']`), but accepts input in the form of string `a b c`:

```php
$explode = fn($v) => explode(' ', $v);

$schema = Expect::arrayOf('string')
	->before($explode);

$normalized = $processor->process($schema, 'a b c');
// OK and returns ['a', 'b', 'c']
```

An element has one handler: a second call to `before()` replaces the first one and raises a deprecation notice (since version 1.4), because in version 2.0 the handlers will be chained, each receiving the result of the previous one. Put all the normalization into one handler instead.


Mapping to Objects: from()
--------------------------

You can have the structure schema generated from a class. Example:

```php
class Config
{
	public string $name;
	public string|null $password = null;
	public bool $admin = false;
}

$schema = Expect::from(new Config);

$data = [
	'name' => 'Frank',
];

$normalized = $processor->process($schema, $data);
// $normalized instanceof Config
// $normalized = {'name' => 'Frank', 'password' => null, 'admin' => false}
```

Anonymous classes are also supported:

```php
$schema = Expect::from(new class {
	public string $name;
	public ?string $password = null;
	public bool $admin = false;
});
```

Because the information obtained from the class definition may not be sufficient, you can supplement the elements with your own schema using the second parameter:

```php
$schema = Expect::from(new Config, [
	'name' => Expect::string()->pattern('\w:.*'),
]);
```


Merging Multiple Configurations
-------------------------------

Applications often assemble their configuration in layers: there are built-in default values, and on top of them the user supplies their own settings, which should override only the items they actually specify. That is exactly what `processMultiple()` does - it takes several datasets, merges them in order so that later ones take precedence, and validates the final result as a whole:

```php
$schema = Expect::structure([
	'host' => Expect::string(),
	'port' => Expect::int(),
	'logging' => Expect::bool(),
]);

$defaults = ['host' => 'localhost', 'port' => 3306, 'logging' => false];
$userConfig = ['port' => 5432, 'logging' => true];

$config = $processor->processMultiple($schema, [$defaults, $userConfig]);
// $config = {'host' => 'localhost', 'port' => 5432, 'logging' => true}
```

The `host` item keeps its default value because the user did not set it, while `port` and `logging` are overwritten by the later dataset. Values stored under string keys are merged this way; the items of a list (`listOf()`, `arrayOf()`) are appended one after another instead of being overwritten.

Arrays and structures can set their strategy with `mergeMode()` (since version 1.4):

```php
use Nette\Schema\MergeMode;

Expect::arrayOf('string')->mergeMode(MergeMode::Replace); // the later array replaces the earlier one
```

`MergeMode::AppendKeys` merges by keys and appends list items, `OverwriteKeys` merges by keys and overwrites list items by position, and `Replace` keeps only the later value. Without `mergeMode()`, an array whose items are not described is merged deeply. For anything else, `mergeWith()` takes your own function combining the later value with the earlier one:

```php
Expect::bool()->mergeWith(fn($value, $base) => $value || $base); // once enabled, stays enabled
```


Exporting to JSON Schema
------------------------

Your schema is already a precise description of what the configuration may contain, and that description is useful outside PHP as well. `JsonSchema::export()` (since version 1.4) turns it into a [JSON Schema](https://json-schema.org) document, the format editors read to offer completion and underline mistakes directly in the JSON or YAML file:

```php
use Nette\Schema\JsonSchema;

$schema = Expect::structure([
	'host' => Expect::string('localhost')->description('Database server hostname'),
	'port' => Expect::int(3306)->min(1)->max(65535),
	'user' => Expect::string()->required(),
]);

echo json_encode(JsonSchema::export($schema), JSON_PRETTY_PRINT);
```

```json
{
	"type": "object",
	"properties": {
		"host": {"type": "string", "description": "Database server hostname"},
		"port": {"type": "integer", "minimum": 1, "maximum": 65535},
		"user": {"type": "string"}
	},
	"required": ["user"],
	"additionalProperties": false
}
```

The export describes the **shape** of the data: types, ranges, patterns, required items, enum values and the texts you added with `description()`. Default values, `castTo()` and `transform()` are deliberately left out - they describe what happens to the data once it has been validated, and JSON Schema has no way to say that.

For the same reason, a type that exists only in PHP - a class such as `Expect::type(Address::class)`, a callable or a resource - throws `Nette\NotSupportedException` instead of producing a document that would quietly claim something else.

Two differences remain between the export and the validation. JSON Schema counts the length of a string in characters, while `Expect::string()` counts bytes, so use `Expect::unicode()` for text that is not plain ASCII. And a `pattern()` is exported as it is, but a JSON Schema validator reads it as an ECMAScript regular expression, so constructions specific to PCRE may be understood differently there.


Under the Hood: normalize, merge, complete
------------------------------------------

Every schema element - whether built-in or one you write yourself - implements four methods that together define how it handles data. Three of them form the processing pipeline:

1. **normalize()** - prepares the raw input. This is where `before()` hooks run and where, for example, an object is turned into an array. It runs first, separately on each dataset.
2. **merge()** - combines two already normalized datasets, with the later one taking priority. This step is used only by `processMultiple()`; `process()` skips it, because it has just a single dataset.
3. **complete()** - performs the actual validation, fills in default values for missing items, and applies `assert()`, `transform()` and `castTo()`. It runs last, on the merged result.

The fourth method, **completeDefault()**, is called by the parent element for an item entirely missing from the input - it either supplies the default value or reports that a `required()` item is missing.

So `process()` runs *normalize → complete*, while `processMultiple()` runs *normalize (each dataset) → merge → complete*. This order is why `before()` sees the raw input, whereas `transform()` sees the already validated value.


Custom Schema Elements
----------------------

You can get a long way with `assert()`, `transform()` and `before()`, so you rarely need to build anything from scratch. But when you want a reusable, self-contained element with its own validation and merging logic, you can create one by implementing the [Nette\Schema\Schema](https://api.nette.org/schema/master/Nette/Schema/Schema.html) interface. It has exactly the four methods described above:

```php
interface Schema
{
	function normalize(mixed $value, Context $context);
	function merge(mixed $value, mixed $base);
	function complete(mixed $value, Context $context);
	function completeDefault(Context $context);
}
```

Errors are not thrown; instead you report them through the [Nette\Schema\Context](https://api.nette.org/schema/master/Nette/Schema/Context.html) object using `$context->addError()` and return `null`. The `Processor` gathers all errors and throws them together at the end.

As an example, let's build a reusable element for byte sizes in the friendly notation people use in configuration files - `'512kB'`, `'2 MB'` - which validates them and returns the number of bytes:

```php
use Nette\Schema\Context;
use Nette\Schema\Schema;

class ByteSizeSchema implements Schema
{
	private const Units = ['b' => 1, 'kb' => 1024, 'mb' => 1024 ** 2, 'gb' => 1024 ** 3];

	public function normalize(mixed $value, Context $context): mixed
	{
		return is_int($value) ? "$value B" : $value; // unify both notations
	}

	public function merge(mixed $value, mixed $base): mixed
	{
		return $value ?? $base; // the later value wins
	}

	public function complete(mixed $value, Context $context): mixed
	{
		if (!is_string($value) || !preg_match('~^(\d+)\s*([a-zA-Z]+)$~', $value, $m)) {
			$context->addError("The item %path% expects to be a size like '512kB'.", 'size.format');
			return null;
		}

		$unit = self::Units[strtolower($m[2])] ?? null;
		if ($unit === null) {
			$context->addError("The item %path% uses an unknown unit '$m[2]'.", 'size.unit');
			return null;
		}

		return (int) $m[1] * $unit;
	}

	public function completeDefault(Context $context): mixed
	{
		return null; // value used when the item is missing from the input
	}
}
```

You can use it anywhere a built-in element is expected - on its own or as part of a larger structure:

```php
$schema = Expect::structure([
	'maxUpload' => new ByteSizeSchema,
	'diskQuota' => new ByteSizeSchema,
]);

$processor->process($schema, ['maxUpload' => '2 MB', 'diskQuota' => 512]);
// OK, returns {'maxUpload' => 2097152, 'diskQuota' => 512}

$processor->process($schema, ['maxUpload' => '5 TB']);
// ERROR: The item 'maxUpload' uses an unknown unit 'TB'.
```

Because the element implements the whole interface, it also works automatically inside `processMultiple()` - the `Processor` calls its `merge()` method just like for any other element. Only `JsonSchema::export()` cannot know what such an element accepts, so it throws `Nette\NotSupportedException`.
