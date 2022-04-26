<?php

namespace Facade\Ignition\Commands;

use Composer\InstalledVersions;
use Exception;
use Facade\FlareClient\Flare;
use Facade\FlareClient\Http\Exceptions\BadResponseCode;
use Illuminate\Config\Repository;
use Illuminate\Console\Command;
use Illuminate\Log\LogManager;

class TestCommand extends Command
{
    protected $signature = 'flare:test';

    protected $description = 'Send a test notification to Flare';

    /** @var \Illuminate\Config\Repository */
    protected $config;

    public function handle(Repository $config)
    {
        $this->config = $config;

        $this->checkFlareKey();

        if (app()->make('log') instanceof LogManager) {
            $this->checkFlareLogger();
        }

        $this->sendTestException();
    }

    protected function checkFlareKey()
    {
        $message = empty($this->config->get('flare.key'))
            ? '❌ Flare key not specified. Make sure you specify a value in the `key` key of the `flare` config file.'
            : '✅ Flare key specified';

        $this->info($message);

        return $this;
    }

    public function checkFlareLogger()
    {
        $defaultLogChannel = $this->config->get('logging.default');

        $activeStack = $this->config->get("logging.channels.{$defaultLogChannel}");

        if (is_null($activeStack)) {
            $this->info("❌ The default logging channel `{$defaultLogChannel}` is not configured in the `logging` config file");
        }

        if (! isset($activeStack['channels']) || ! in_array('flare', $activeStack['channels'])) {
            $this->info("❌ The logging channel `{$defaultLogChannel}` does not contain the 'flare' channel");
        }

        if (is_null($this->config->get('logging.channels.flare'))) {
            $this->info('❌ There is no logging channel named `flare` in the `logging` config file');
        }

        if ($this->config->get('logging.channels.flare.driver') !== 'flare') {
            $this->info('❌ The `flare` logging channel defined in the `logging` config file is not set to `flare`.');
        }

        $this->info('✅ The Flare logging driver was configured correctly.');

        return $this;
    }

    protected function sendTestException()
    {
        $testException = new Exception('This is an exception to test if the integration with Flare works.');

        try {
            app(Flare::class)->sendTestReport($testException);
            $this->info('');
        } catch (Exception $exception) {
            $this->warn('❌ We were unable to send an exception to Flare. ');

            if ($exception instanceof BadResponseCode) {
                $this->info('');
                $message = 'Unknown error';

                $body = $exception->response->getBody();

                if (is_array($body) && isset($body['message'])) {
                    $message = $body['message'];
                }

                $this->warn("{$exception->response->getHttpResponseCode()} - {$message}");
            } else {
                $this->warn($exception->getMessage());
            }

            $this->warn('Make sure that your key is correct and that you have a valid subscription.');
            $this->info('');
            $this->info('For more info visit the docs on https://flareapp.io/docs/ignition-for-laravel/introduction');
            $this->info('You can see the status page of Flare at https://status.flareapp.io');
            $this->info('Flare support can be reached at support@flareapp.io');

            $this->line('');
            $this->line('Extra info');
            $this->table([], [
                ['Platform', PHP_OS],
                ['PHP', phpversion()],
                ['Laravel', app()->version()],
                ['facade/ignition', InstalledVersions::getVersion('facade/ignition')],
                ['facade/flare-client-php', InstalledVersions::getVersion('facade/flare-client-php')],
                ['Curl', curl_version()['version']],
                ['SSL', curl_version()['ssl_version']],
            ]);

            if ($this->output->isVerbose()) {
                throw $exception;
            }

            return;
        }

        $this->info('We tried to send an exception to Flare. Please check if it arrived!');
    }
}
