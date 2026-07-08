Symfony Polyfill / DeepClone
============================

This package provides a pure-PHP implementation of the functions and exception
classes from the [deepclone extension](https://github.com/symfony/php-ext-deepclone):

- `deepclone_to_array(mixed $value, ?array $allowed_classes = null, bool $allow_named_closures = false): array`
  — converts any serializable PHP value graph into a pure array (only scalars
  and nested arrays, no objects).
- `deepclone_from_array(array $data, ?array $allowed_classes = null, bool $allow_named_closures = false): mixed`
  — rebuilds the value graph from the array, preserving object identity,
  references, cycles, and private property state.
- `DeepClone\NotInstantiableException` and `DeepClone\ClassNotFoundException`

When the native `deepclone` extension is loaded, this polyfill does nothing —
the extension provides the same functions 4–5× faster.

The array representation preserves PHP's copy-on-write for strings and scalar
arrays, making repeated cloning of a prototype significantly more memory
efficient than `unserialize(serialize())`. It also serves as a cache-friendly
serialization format: when dumped via `var_export()` into a `.php` file,
OPcache maps the array into shared memory, making deserialization essentially
free.

This is the same wire format used by Symfony's `VarExporter\DeepCloner`.

More information can be found in the
[main Polyfill README](https://github.com/symfony/polyfill/blob/main/README.md).

License
=======

This library is released under the [MIT license](LICENSE).
