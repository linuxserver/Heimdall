<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Command;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Completion\CompletionInput;
use Symfony\Component\Console\Completion\CompletionSuggestions;
use Symfony\Component\Console\Exception\ExceptionInterface;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Base class for all commands.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Command
{
    // see https://tldp.org/LDP/abs/html/exitcodes.html
    public const SUCCESS = 0;
    public const FAILURE = 1;
    public const INVALID = 2;

    /**
     * @var string|null The default command name
     */
    protected static $defaultName;

    /**
     * @var string|null The default command description
     */
    protected static $defaultDescription;

    private $application;
    private $name;
    private $processTitle;
    private $aliases = [];
    private $definition;
    private $hidden = false;
    private $help = '';
    private $description = '';
    private $fullDefinition;
    private $ignoreValidationErrors = false;
    private $code;
    private $synopsis = [];
    private $usages = [];
    private $helperSet;

    /**
     * @return string|null
     */
    public static function getDefaultName()
    {
        $class = static::class;

        if (\PHP_VERSION_ID >= 80000 && $attribute = (new \ReflectionClass($class))->getAttributes(AsCommand::class)) {
            return $attribute[0]->newInstance()->name;
        }

        $r = new \ReflectionProperty($class, 'defaultName');

        return $class === $r->class ? static::$defaultName : null;
    }

    public static function getDefaultDescription(): ?string
    {
        $class = static::class;

        if (\PHP_VERSION_ID >= 80000 && $attribute = (new \ReflectionClass($class))->getAttributes(AsCommand::class)) {
            return $attribute[0]->newInstance()->description;
        }

        $r = new \ReflectionProperty($class, 'defaultDescription');

        return $class === $r->class ? static::$defaultDescription : null;
    }

    /**
     * @param string|null $name The name of the command; passing null means it must be set in configure()
     *
     * @throws LogicException When the command name is empty
     */
    public function __construct(string $name = null)
    {
        $this->definition = new InputDefinition();

        if (null === $name && null !== $name = static::getDefaultName()) {
            $aliases = explode('|', $name);

            if ('' === $name = array_shift($aliases)) {
                $this->setHidden(true);
                $name = array_shift($aliases);
            }

            $this->setAliases($aliases);
        }

        if (null !== $name) {
            $this->setName($name);
        }

        if ('' === $this->description) {
            $this->setDescription(static::getDefaultDescription() ?? '');
        }

        $this->configure();
    }

    /**
     * Ignores validation errors.
     *
     * This is mainly useful for the help command.
     */
    public function ignoreValidationErrors()
    {
        $this->ignoreValidationErrors = true;
    }

    public function setApplication(Application $application = null)
    {
        $this->application = $application;
        if ($application) {
            $this->setHelperSet($application->getHelperSet());
        } else {
            $this->helperSet = null;
        }

        $this->fullDefinition = null;
    }

    public function setHelperSet(HelperSet $helperSet)
    {
        $this->helperSet = $helperSet;
    }

    /**
     * Gets the helper set.
     *
     * @return HelperSet|null
     */
    public function getHelperSet()
    {
        return $this->helperSet;
    }

    /**
     * Gets the application instance for this command.
     *
     * @return Application|null
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * Checks whether the command is enabled or not in the current environment.
     *
     * Override this to check for x or y and return false if the command cannot
     * run properly under the current conditions.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return true;
    }

    /**
     * Configures the current command.
     */
    protected function configure()
    {
    }

    /**
     * Executes the current command.
     *
     * This method is not abstract because you can use this class
     * as a concrete class. In this case, instead of defining the
     * execute() method, you set the code to execute by passing
     * a Closure to the setCode() method.
     *
     * @return int 0 if everything went fine, or an exit code
     *
     * @throws LogicException When this abstract method is not implemented
     *
     * @see setCode()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        throw new LogicException('You must override the execute() method in the concrete command class.');
    }

    /**
     * Interacts with the user.
     *
     * This method is executed before the InputDefinition is validated.
     * This means that this is the only place where the command can
     * interactively ask for values of missing required arguments.
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
    }

    /**
     * Initializes the command after the input has been bound and before the input
     * is validated.
     *
     * This is mainly useful when a lot of commands extends one main command
     * where some things need to be initialized based on the input arguments and options.
     *
     * @see InputInterface::bind()
     * @see InputInterface::validate()
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
    }

    /**
     * Runs the command.
     *
     * The code to execute is either defined directly with the
     * setCode() method or by overriding the execute() method
     * in a sub-class.
     *
     * @return int The command exit code
     *
     * @throws \Exception When binding input fails. Bypass this by calling {@link ignoreValidationErrors()}.
     *
     * @see setCode()
     * @see execute()
     */
    public function run(InputInterface $input, OutputInterface $output)
    {
        // add the application arguments and options
        $this->mergeApplicationDefinition();

        // bind the input against the command specific arguments/options
        try {
            $input->bind($this->getDefinition());
        } catch (ExceptionInterface $e) {
            if (!$this->ignoreValidationErrors) {
                throw $e;
            }
        }

        $this->initialize($input, $output);

        if (null !== $this->processTitle) {
            if (\function_exists('cli_set_process_title')) {
                if (!@cli_set_process_title($this->processTitle)) {
                    if ('Darwin' === \PHP_OS) {
                        $output->writeln('<comment>Running "cli_set_process_title" as an unprivileged user is not supported on MacOS.</comment>', OutputInterface::VERBOSITY_VERY_VERBOSE);
                    } else {
                        cli_set_process_title($this->processTitle);
                    }
                }
            } elseif (\function_exists('setproctitle')) {
                setproctitle($this->processTitle);
            } elseif (OutputInterface::VERBOSITY_VERY_VERBOSE === $output->getVerbosity()) {
                $output->writeln('<comment>Install the proctitle PECL to be able to change the process title.</comment>');
            }
        }

        if ($input->isInteractive()) {
            $this->interact($input, $output);
        }

        // The command name argument is often omitted when a command is executed directly with its run() method.
        // It would fail the validation if we didn't make sure the command argument is present,
        // since it's required by the application.
        if ($input->hasArgument('command') && null === $input->getArgument('command')) {
            $input->setArgument('command', $this->getName());
        }

        $input->validate();

        if ($this->code) {
            $statusCode = ($this->code)($input, $output);
        } else {
            $statusCode = $this->execute($input, $output);

            if (!\is_int($statusCode)) {
                throw new \TypeError(sprintf('Return value of "%s::execute()" must be of the type int, "%s" returned.', static::class, get_debug_type($statusCode)));
            }
        }

        return is_numeric($statusCode) ? (int) $statusCode : 0;
    }

    /**
     * Adds suggestions to $suggestions for the current completion input (e.g. option or argument).
     */
    public function complete(CompletionInput $input, CompletionSuggestions $suggestions): void
    {
    }

    /**
     * Sets the code to execute when running this command.
     *
     * If this method is used, it overrides the code defined
     * in the execute() method.
     *
     * @param callable $code A callable(InputInterface $input, OutputInterface $output)
     *
     * @return $this
     *
     * @throws InvalidArgumentException
     *
     * @see execute()
     */
    public function setCode(callable $code)
    {
        if ($code instanceof \Closure) {
            $r = new \ReflectionFunction($code);
            if (null === $r->getClosureThis()) {
                set_error_handler(static function () {});
                try {
                    if ($c = \Closure::bind($code, $this)) {
                        $code = $c;
                    }
                } finally {
                    restore_error_handler();
                }
            }
        }

        $this->code = $code;

        return $this;
    }

    /**
     * Merges the application definition with the command definition.
     *
     * This method is not part of public API and should not be used directly.
     *
     * @param bool $mergeArgs Whether to merge or not the Application definition arguments to Command definition arguments
     *
     * @internal
     */
    public function mergeApplicationDefinition(bool $mergeArgs = true)
    {
        if (null === $this->application) {
            return;
        }

        $this->fullDefinition = new InputDefinition();
        $this->fullDefinition->setOptions($this->definition->getOptions());
        $this->fullDefinition->addOptions($this->application->getDefinition()->getOptions());

        if ($mergeArgs) {
            $this->fullDefinition->setArguments($this->application->getDefinition()->getArguments());
            $this->fullDefinition->addArguments($this->definition->getArguments());
        } else {
            $this->fullDefinition->setArguments($this->definition->getArguments());
        }
    }

    /**
     * Sets an array of argument and option instances.
     *
     * @param array|InputDefinition $definition An array of argument and option instances or a definition instance
     *
     * @return $this
     */
    public function setDefinition($definition)
    {
        if ($definition instanceof InputDefinition) {
            $this->definition = $definition;
        } else {
            $this->definition->setDefinition($definition);
        }

        $this->fullDefinition = null;

        return $this;
    }

    /**
     * Gets the InputDefinition attached to this Command.
     *
     * @return InputDefinition
     */
    public function getDefinition()
    {
        return $this->fullDefinition ?? $this->getNativeDefinition();
    }

    /**
     * Gets the InputDefinition to be used to create representations of this Command.
     *
     * Can be overridden to provide the original command representation when it would otherwise
     * be changed by merging with the application InputDefinition.
     *
     * This method is not part of public API and should not be used directly.
     *
     * @return InputDefinition
     */
    public function getNativeDefinition()
    {
        if (null === $this->definition) {
            throw new LogicException(sprintf('Command class "%s" is not correctly initialized. You probably forgot to call the parent constructor.', static::class));
        }

        return $this->definition;
    }

    /**
     * Adds an argument.
     *
     * @param int|null $mode    The argument mode: InputArgument::REQUIRED or InputArgument::OPTIONAL
     * @param mixed    $default The default value (for InputArgument::OPTIONAL mode only)
     *
     * @throws InvalidArgumentException When argument mode is not valid
     *
     * @return $this
     */
    public function addArgument(string $name, int $mode = null, string $description = '', $default = null)
    {
        $this->definition->addArgument(new InputArgument($name, $mode, $description, $default));
        if (null !== $this->fullDefinition) {
            $this->fullDefinition->addArgument(new InputArgument($name, $mode, $description, $default));
        }

        return $this;
    }

    /**
     * Adds an option.
     *
     * @param string|array|null $shortcut The shortcuts, can be null, a string of shortcuts delimited by | or an array of shortcuts
     * @param int|null          $mode     The option mode: One of the InputOption::VALUE_* constants
     * @param mixed             $default  The default value (must be null for InputOption::VALUE_NONE)
     *
     * @throws InvalidArgumentException If option mode is invalid or incompatible
     *
     * @return $this
     */
    public function addOption(string $name, $shortcut = null, int $mode = null, string $description = '', $default = null)
    {
        $this->definition->addOption(new InputOption($name, $shortcut, $mode, $description, $default));
        if (null !== $this->fullDefinition) {
            $this->fullDefinition->addOption(new InputOption($name, $shortcut, $mode, $description, $default));
        }

        return $this;
    }

    /**
     * Sets the name of the command.
     *
     * This method can set both the namespace and the name if
     * you separate them by a colon (:)
     *
     *     $command->setName('foo:bar');
     *
     * @return $this
     *
     * @throws InvalidArgumentException When the name is invalid
     */
    public function setName(string $name)
    {
        $this->validateName($name);

        $this->name = $name;

        return $this;
    }

    /**
     * Sets the process title of the command.
     *
     * This feature should be used only when creating a long process command,
     * like a daemon.
     *
     * @return $this
     */
    public function setProcessTitle(string $title)
    {
        $this->processTitle = $title;

        return $this;
    }

    /**
     * Returns the command name.
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param bool $hidden Whether or not the command should be hidden from the list of commands
     *                     The default value will be true in Symfony 6.0
     *
     * @return $this
     *
     * @final since Symfony 5.1
     */
    public function setHidden(bool $hidden /*= true*/)
    {
        $this->hidden = $hidden;

        return $this;
    }

    /**
     * @return bool whether the command should be publicly shown or not
     */
    public function isHidden()
    {
        return $this->hidden;
    }

    /**
     * Sets the description for the command.
     *
     * @return $this
     */
    public function setDescription(string $description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Returns the description for the command.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets the help for the command.
     *
     * @return $this
     */
    public function setHelp(string $help)
    {
        $this->help = $help;

        return $this;
    }

    /**
     * Returns the help for the command.
     *
     * @return string
     */
    public function getHelp()
    {
        return $this->help;
    }

    /**
     * Returns the processed help for the command replacing the %command.name% and
     * %command.full_name% patterns with the real values dynamically.
     *
     * @return string
     */
    public function getProcessedHelp()
    {
        $name = $this->name;
        $isSingleCommand = $this->application && $this->application->isSingleCommand();

        $placeholders = [
            '%command.name%',
            '%command.full_name%',
        ];
        $replacements = [
            $name,
            $isSingleCommand ? $_SERVER['PHP_SELF'] : $_SERVER['PHP_SELF'].' '.$name,
        ];

        return str_replace($placeholders, $replacements, $this->getHelp() ?: $this->getDescription());
    }

    /**
     * Sets the aliases for the command.
     *
     * @param string[] $aliases An array of aliases for the command
     *
     * @return $this
     *
     * @throws InvalidArgumentException When an alias is invalid
     */
    public function setAliases(iterable $aliases)
    {
        $list = [];

        foreach ($aliases as $alias) {
            $this->validateName($alias);
            $list[] = $alias;
        }

        $this->aliases = \is_array($aliases) ? $aliases : $list;

        return $this;
    }

    /**
     * Returns the aliases for the command.
     *
     * @return array
     */
    public function getAliases()
    {
        return $this->aliases;
    }

    /**
     * Returns the synopsis for the command.
     *
     * @param bool $short Whether to show the short version of the synopsis (with options folded) or not
     *
     * @return string
     */
    public function getSynopsis(bool $short = false)
    {
        $key = $short ? 'short' : 'long';

        if (!isset($this->synopsis[$key])) {
            $this->synopsis[$key] = trim(sprintf('%s %s', $this->name, $this->definition->getSynopsis($short)));
        }

        return $this->synopsis[$key];
    }

    /**
     * Add a command usage example, it'll be prefixed with the command name.
     *
     * @return $this
     */
    public function addUsage(string $usage)
    {
        if (!str_starts_with($usage, $this->name)) {
            $usage = sprintf('%s %s', $this->name, $usage);
        }

        $this->usages[] = $usage;

        return $this;
    }

    /**
     * Returns alternative usages of the command.
     *
     * @return array
     */
    public function getUsages()
    {
        return $this->usages;
    }

    /**
     * Gets a helper instance by name.
     *
     * @return mixed
     *
     * @throws LogicException           if no HelperSet is defined
     * @throws InvalidArgumentException if the helper is not defined
     */
    public function getHelper(string $name)
    {
        if (null === $this->helperSet) {
            throw new LogicException(sprintf('Cannot retrieve helper "%s" because there is no HelperSet defined. Did you forget to add your command to the application or to set the application on the command using the setApplication() method? You can also set the HelperSet directly using the setHelperSet() method.', $name));
        }

        return $this->helperSet->get($name);
    }

    /**
     * Validates a command name.
     *
     * It must be non-empty and parts can optionally be separated by ":".
     *
     * @throws InvalidArgumentException When the name is invalid
     */
    private function validateName(string $name)
    {
        if (!preg_match('/^[^\:]++(\:[^\:]++)*$/', $name)) {
            throw new InvalidArgumentException(sprintf('Command name "%s" is invalid.', $name));
        }
    }
}
