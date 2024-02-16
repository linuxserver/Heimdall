<?php

namespace Laravel\Prompts;

use Closure;
use Illuminate\Support\Collection;

/**
 * Prompt the user for text input.
 */
function text(string $label, string $placeholder = '', string $default = '', bool|string $required = false, mixed $validate = null, string $hint = ''): string
{
    return (new TextPrompt(...func_get_args()))->prompt();
}

/**
 * Prompt the user for input, hiding the value.
 */
function password(string $label, string $placeholder = '', bool|string $required = false, mixed $validate = null, string $hint = ''): string
{
    return (new PasswordPrompt(...func_get_args()))->prompt();
}

/**
 * Prompt the user to select an option.
 *
 * @param  array<int|string, string>|Collection<int|string, string>  $options
 * @param  true|string  $required
 */
function select(string $label, array|Collection $options, int|string|null $default = null, int $scroll = 5, mixed $validate = null, string $hint = '', bool|string $required = true): int|string
{
    return (new SelectPrompt(...func_get_args()))->prompt();
}

/**
 * Prompt the user to select multiple options.
 *
 * @param  array<int|string, string>|Collection<int|string, string>  $options
 * @param  array<int|string>|Collection<int, int|string>  $default
 * @return array<int|string>
 */
function multiselect(string $label, array|Collection $options, array|Collection $default = [], int $scroll = 5, bool|string $required = false, mixed $validate = null, string $hint = 'Use the space bar to select options.'): array
{
    return (new MultiSelectPrompt(...func_get_args()))->prompt();
}

/**
 * Prompt the user to confirm an action.
 */
function confirm(string $label, bool $default = true, string $yes = 'Yes', string $no = 'No', bool|string $required = false, mixed $validate = null, string $hint = ''): bool
{
    return (new ConfirmPrompt(...func_get_args()))->prompt();
}

/**
 * Prompt the user for text input with auto-completion.
 *
 * @param  array<string>|Collection<int, string>|Closure(string): array<string>  $options
 */
function suggest(string $label, array|Collection|Closure $options, string $placeholder = '', string $default = '', int $scroll = 5, bool|string $required = false, mixed $validate = null, string $hint = ''): string
{
    return (new SuggestPrompt(...func_get_args()))->prompt();
}

/**
 * Allow the user to search for an option.
 *
 * @param  Closure(string): array<int|string, string>  $options
 * @param  true|string  $required
 */
function search(string $label, Closure $options, string $placeholder = '', int $scroll = 5, mixed $validate = null, string $hint = '', bool|string $required = true): int|string
{
    return (new SearchPrompt(...func_get_args()))->prompt();
}

/**
 * Allow the user to search for multiple option.
 *
 * @param  Closure(string): array<int|string, string>  $options
 * @return array<int|string>
 */
function multisearch(string $label, Closure $options, string $placeholder = '', int $scroll = 5, bool|string $required = false, mixed $validate = null, string $hint = 'Use the space bar to select options.'): array
{
    return (new MultiSearchPrompt(...func_get_args()))->prompt();
}

/**
 * Render a spinner while the given callback is executing.
 *
 * @template TReturn of mixed
 *
 * @param  \Closure(): TReturn  $callback
 * @return TReturn
 */
function spin(Closure $callback, string $message = ''): mixed
{
    return (new Spinner($message))->spin($callback);
}

/**
 * Display a note.
 */
function note(string $message, ?string $type = null): void
{
    (new Note($message, $type))->display();
}

/**
 * Display an error.
 */
function error(string $message): void
{
    (new Note($message, 'error'))->display();
}

/**
 * Display a warning.
 */
function warning(string $message): void
{
    (new Note($message, 'warning'))->display();
}

/**
 * Display an alert.
 */
function alert(string $message): void
{
    (new Note($message, 'alert'))->display();
}

/**
 * Display an informational message.
 */
function info(string $message): void
{
    (new Note($message, 'info'))->display();
}

/**
 * Display an introduction.
 */
function intro(string $message): void
{
    (new Note($message, 'intro'))->display();
}

/**
 * Display a closing message.
 */
function outro(string $message): void
{
    (new Note($message, 'outro'))->display();
}

/**
 * Display a table.
 *
 * @param  array<int, string|array<int, string>>|Collection<int, string|array<int, string>>  $headers
 * @param  array<int, array<int, string>>|Collection<int, array<int, string>>  $rows
 */
function table(array|Collection $headers = [], array|Collection|null $rows = null): void
{
    (new Table($headers, $rows))->display();
}

/**
 * Display a progress bar.
 *
 * @template TSteps of iterable<mixed>|int
 * @template TReturn
 *
 * @param  TSteps  $steps
 * @param  ?Closure((TSteps is int ? int : value-of<TSteps>), Progress<TSteps>): TReturn  $callback
 * @return ($callback is null ? Progress<TSteps> : array<TReturn>)
 */
function progress(string $label, iterable|int $steps, ?Closure $callback = null, string $hint = ''): array|Progress
{
    $progress = new Progress($label, $steps, $hint);

    if ($callback !== null) {
        return $progress->map($callback);
    }

    return $progress;
}
