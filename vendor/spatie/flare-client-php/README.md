# Send PHP errors to Flare

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/flare-client-php.svg?style=flat-square)](https://packagist.org/packages/spatie/flare-client-php)
[![Run tests](https://github.com/spatie/flare-client-php/actions/workflows/run-tests.yml/badge.svg)](https://github.com/spatie/flare-client-php/actions/workflows/run-tests.yml)
[![PHPStan](https://github.com/spatie/flare-client-php/actions/workflows/phpstan.yml/badge.svg)](https://github.com/spatie/flare-client-php/actions/workflows/phpstan.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/flare-client-php.svg?style=flat-square)](https://packagist.org/packages/spatie/flare-client-php)

This repository contains the PHP client to send errors and exceptions to [Flare](https://flareapp.io). The client can be installed using composer and works for PHP 8.0 and above.

Using Laravel? You probably want to use [Ignition for Laravel](https://github.com/spatie/laravel-ignition). It comes with a beautiful error page and has the Flare client built in.

![Screenshot of error in Flare](https://facade.github.io/flare-client-php/screenshot.png)

## Documentation

When creating a new project on Flare, we'll display installation instructions for your PHP app. Even though the default settings will work fine for all projects, we offer some customization options that you might like.

### Ignoring errors

The Flare client will always send all exceptions to Flare, you can change this behaviour by filtering the exceptions with a callable:

```php
// Where you registered your client...
$flare = Flare::make('YOUR-FLARE-API-KEY')
	->registerFlareHandlers();

$flare->filterExceptionsUsing(
    fn(Throwable $throwable) =>  !$throwable instanceof AuthorizationException
);
```

Additionally, you can provide a callable to the `Flare::filterReportsUsing` method to stop a report from being sent to Flare. Compared to `filterExceptionsCallable`, this can also prevent logs and errors from being sent.

```php
use Spatie\FlareClient\Flare;

$flare = Flare::make('YOUR-API-KEY')
  ->registerFlareHandlers();

Flare::filterReportsUsing(function(Report $report)  {
    // return a boolean to control whether the report should be sent to Flare
    return true;
});
```

Finally, it is also possible to set the levels of errors reported to Flare as such:

```php
$flare->reportErrorLevels(E_ALL & ~E_NOTICE); // Will send all errors except E_NOTICE errors
```

### Controlling collected data

Just like the Laravel configuration, the generic PHP client allows you to configure which information should be sent to Flare.

#### Anonymizing IPs

By default, the Flare client collects information about the IP address of your application users. If you want to disable this information, you can call the `anonymizeIp()` method on your Flare client instance.

```php
// Where you registered your client...
$flare = Flare::make('YOUR-FLARE-API-KEY')
	->registerFlareHandlers();

$flare->anonymizeIp();
```

#### Censoring request body fields

When an exception occurs in a web request, the Flare client will pass on any request fields that are present in the body.

In some cases, such as a login page, these request fields may contain a password that you don't want to send to Flare.

To censor out values of certain fields, you can use call `censorRequestBodyFields`. You should pass it the names of the fields you wish to censor.

```php
// Where you registered your client...
$flare = Flare::make('YOUR-FLARE-API-KEY')
	->registerFlareHandlers();

$flare->censorRequestBodyFields('password');
```

This will replace the value of any sent fields named "password" with the value "<CENSORED>".

### Identifying users

When reporting an error to Flare, you can tell the Flare client, what information you have about the currently authenticated user. You can do this by providing a `user` group that holds all the information you want to share.

```php
$user = YourAuthenticatedUserInstance();

$flare->group('user', [
	'email' => $user->email,
	'name' => $user->name,
	'additional_information' => $user->additional,
]);
```

### Linking to errors

When an error occurs in web request, your application will likely display a minimal error page when it's in production.

If a user sees this page and wants to report this error to you, the user usually only reports the URL and the time the error was seen.

To let your users pinpoint the exact error they saw, you can display the UUID of the error sent to Flare.

You can do this by displaying the UUID returned by `Flare::sentReports()->latestUuid()` in your view. Optionally, you can use `Flare::sentReports()->latestUrl()` to get a link to the error in Flare. That link isn't publicly accessible, it is only visible to Flare users that have access to the project on Flare.

In certain cases, multiple error can be reported to Flare in a single request. To get a hold of the UUIDs of all sent errors, you can call `Flare::sentReports()->uuids()`. You can get links to all sent errors with `Flare::sentReports()->urls()`.

It is possible to search for certain errors in Flare using the UUID, you can find more information about that [here](http://flareapp.io/docs/flare/general/searching-errors).

### Adding custom context

When you send an error to Flare within a non-Laravel application,  we do not collect your application information - since we don't know about your specific application.
In order to provide more information, you can add custom context to your application that will be sent along with every exception that happens in your application. This can be very useful if you want to provide key-value related information that furthermore helps you to debug a possible exception.

The Flare client allows you to set custom context items like this:

```php
// Get access to your registered Flare client instance
$flare->context('Tenant', 'My-Tenant-Identifier');
```

This could for example be set automatically in a Laravel service provider or an event. So the next time an exception happens, this value will be sent along to Flare and you can find it on the "Context" tab.

#### Grouping multiple context items

Sometimes you may want to group your context items by a key that you provide to have an easier visual differentiation when you look at your custom context items.

The Flare client allows you to also provide your own custom context groups like this:

```php
// Get access to your registered Flare client instance
$flare->group('Custom information', [
    'key' => 'value',
    'another key' => 'another value',
]);
```

### Adding glows

In addition to custom context items, you can also add "Glows" to your application.
Glows allow you to add little pieces of information, that can later be found in a chronological order in the "Debug" tab of your application.

You can think of glows as breadcrumbs that can help you track down which parts of your code an exception went through.

The Flare PHP client allows you to add a glows to your application like this:


### Stacktrace arguments


When an error occurs in your application, Flare will send the stacktrace of the error to Flare. This stacktrace contains the file and line number where the error occurred and the argument values passed to the function or method that caused the error.

These argument values have been significantly reduced to make them easier to read and reduce the amount of data sent to Flare, which means that the arguments are not always complete. To see the full arguments, you can always use a glow to send the whole parameter to Flare.

For example, let's say you have the following Carbon object:

```php
new DateTime('2020-05-16 14:00:00', new DateTimeZone('Europe/Brussels'))
```

Flare will automatically reduce this to the following:

```
16 May 2020 14:00:00 +02:00
```

It is possible to configure how these arguments are reduced. You can even implement your own reducers!

By default, the following reducers are used:

- BaseTypeArgumentReducer
- ArrayArgumentReducer
- StdClassArgumentReducer
- EnumArgumentReducer
- ClosureArgumentReducer
- DateTimeArgumentReducer
- DateTimeZoneArgumentReducer
- SymphonyRequestArgumentReducer
- StringableArgumentReducer

#### Implementing your reducer

Each reducer implements `Spatie\FlareClient\Arguments\Reducers\ArgumentReducer`. This interface contains a single method, `execute` which provides the original argument value:

```php
interface ArgumentReducer
{
    public function execute(mixed $argument): ReducedArgumentContract;
}
```

In the end, three types of values can be returned:

When the reducer could not reduce this type of argument value:

```php
return UnReducedArgument::create();
```

When the reducer could reduce the argument value, but a part was truncated due to the size:

```php
return new TruncatedReducedArgument(
    array_slice($argument, 0, 25), // The reduced value
    'array' // The original type of the argument
);
```

When the reducer could reduce the full argument value:

```php
return new TruncatedReducedArgument(
    $argument, // The reduced value
    'array' // The original type of the argument
);
```

For example, the `DateTimeArgumentReducer` from the example above looks like this:

```php
class DateTimeArgumentReducer implements ArgumentReducer
{
    public function execute(mixed $argument): ReducedArgumentContract
    {
        if (! $argument instanceof \DateTimeInterface) {
            return UnReducedArgument::create();
        }
        
        return new ReducedArgument(
            $argument->format('d M Y H:i:s p'),
            get_class($argument),
        );
    }
}
```

#### Configuring the reducers

Reducers can be added as such:

```php
// Where you registered your client...
$flare = Flare::make('YOUR-FLARE-API-KEY')
 ->registerFlareHandlers();

$flare->argumentReducers([
    BaseTypeArgumentReducer::class,
    ArrayArgumentReducer::class,
    StdClassArgumentReducer::class,
    EnumArgumentReducer::class,
    ClosureArgumentReducer::class,
    DateTimeArgumentReducer::class,
    DateTimeZoneArgumentReducer::class,
    SymphonyRequestArgumentReducer::class,
    StringableArgumentReducer::class,
])
```

Reducers are executed from top to bottom. The first reducer which doesn't return an `UnReducedArgument` will be used. Always add the default reducers when you want to define your own reducer. Otherwise, a very rudimentary reduced argument value will be used.

#### Disabling stack frame arguments

If you don't want to send any arguments to Flare, you can turn off this behavior as such:

```php
// Where you registered your client...
$flare = Flare::make('YOUR-FLARE-API-KEY')
 ->registerFlareHandlers();

$flare->withStackFrameArguments(false);
```

#### Missing arguments?

- Make sure you've got the latest version of Flare / Ignition
- Check that `withStackFrameArguments` is not disabled
- Check your ini file whether `zend.exception_ignore_args` is enabled, it should be `0`


```php
use Spatie\FlareClient\Enums\MessageLevels;

// Get access to your registered Flare client instance
$flare->glow('This is a message from glow!', MessageLevels::DEBUG, func_get_args());
```

### Handling exceptions

When an exception is thrown in an application, the application stops executing and the exception is reported to Flare.
However, there are cases where you might want to handle the exception so that the application can continue running. And
the user isn't presented with an error message.

In such cases it might still be useful to report the exception to Flare, so you'll have a correct overview of what's
going on within your application. We call such exceptions "handled exceptions".

When you've caught an exception in PHP it can still be reported to Flare:

```php
try {
    // Code that might throw an exception
} catch (Exception $exception) {
    $flare->reportHandled($exception);
}
```

In Flare, we'll show that the exception was handled, it is possible to filter these exceptions. You'll find more about filtering exceptions [here](https://flareapp.io/docs/flare/general/searching-errors).

### Writing custom middleware

Before Flare receives the data that was collected from your local exception, we give you the ability to call custom middleware methods.
These methods retrieve the report that should be sent to Flare and allow you to add custom information to that report.

Just like with the Flare client itself, you can add custom context information to your report as well. This allows you to structure your code so that you have all context related changes in one place.

You can register a custom middleware by using the `registerMiddleware` method on the `Spatie\FlareClient\Flare` class, like this:

```php
use Spatie\FlareClient\Report;

// Get access to your registered Flare client instance
$flare->registerMiddleware(function (Report $report, $next) {
    // Add custom information to the report
    $report->context('key', 'value');

    return $next($report);
});
```

To create a middleware that, for example, removes all the session data before your report gets sent to Flare, the middleware implementation might look like this:

```php
use Spatie\FlareClient\Report;

class FlareMiddleware
{
    public function handle(Report $report, $next)
    {
	    $context = $report->allContext();

	    $context['session'] = null;

	    $report->userProvidedContext($context);

	    return $next($report);
    }
}
```

### Identifying users

When reporting an error to Flare, you can tell the Flare client, what information you have about the currently authenticated user. You can do this by providing a `user` group that holds all the information you want to share.

```php
$user = YourAuthenticatedUserInstance();

$flare->group('user', [
	'email' => $user->email,
	'name' => $user->name,
	'additional_information' => $user->additional,
]);
```

### Customizing error grouping

Flare has a [special grouping](https://flareapp.io/docs/flare/general/error-grouping) algorithm that groups similar error occurrences into errors to make understanding what's going on in your application easier.

While the default grouping algorithm works for 99% of the cases, there are some cases where you might want to customize the grouping.

This can be done on an exception class base, you can tell Flare to group all exceptions of a specific class together:

```php
use Spatie\FlareClient\Enums\OverriddenGrouping;

$flare->overrideGrouping(SomeExceptionClass::class, OverriddenGrouping::ExceptionClass);
```

In this case every exception of the `SomeExceptionClass` will be grouped together no matter what the message or stack trace is.

It is also possible to group exceptions of the same class together, but also take the message into account:

```php
use Spatie\FlareClient\Enums\OverriddenGrouping;

$flare->overrideGrouping(SomeExceptionClass::class, OverriddenGrouping::ExceptionMessageAndClass);
```

Be careful when grouping by class and message, since every occurrence might have a slightly different message, this could lead to a lot of different errors.


## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
composer test
```

## Contributing

Please see [CONTRIBUTING](https://github.com/spatie/.github/blob/main/CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email support@flareapp.io instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

