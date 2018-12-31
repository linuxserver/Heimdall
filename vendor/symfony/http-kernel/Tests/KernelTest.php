<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\DependencyInjection\ResettableServicePass;
use Symfony\Component\HttpKernel\DependencyInjection\ServicesResetter;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\Tests\Fixtures\KernelForOverrideName;
use Symfony\Component\HttpKernel\Tests\Fixtures\KernelForTest;
use Symfony\Component\HttpKernel\Tests\Fixtures\KernelWithoutBundles;
use Symfony\Component\HttpKernel\Tests\Fixtures\ResettableService;

class KernelTest extends TestCase
{
    public static function tearDownAfterClass()
    {
        $fs = new Filesystem();
        $fs->remove(__DIR__.'/Fixtures/var');
    }

    public function testConstructor()
    {
        $env = 'test_env';
        $debug = true;
        $kernel = new KernelForTest($env, $debug);

        $this->assertEquals($env, $kernel->getEnvironment());
        $this->assertEquals($debug, $kernel->isDebug());
        $this->assertFalse($kernel->isBooted());
        $this->assertLessThanOrEqual(microtime(true), $kernel->getStartTime());
        $this->assertNull($kernel->getContainer());
    }

    public function testClone()
    {
        $env = 'test_env';
        $debug = true;
        $kernel = new KernelForTest($env, $debug);

        $clone = clone $kernel;

        $this->assertEquals($env, $clone->getEnvironment());
        $this->assertEquals($debug, $clone->isDebug());
        $this->assertFalse($clone->isBooted());
        $this->assertLessThanOrEqual(microtime(true), $clone->getStartTime());
        $this->assertNull($clone->getContainer());
    }

    public function testInitializeContainerClearsOldContainers()
    {
        $fs = new Filesystem();
        $legacyContainerDir = __DIR__.'/Fixtures/var/cache/custom/ContainerA123456';
        $fs->mkdir($legacyContainerDir);
        touch($legacyContainerDir.'.legacy');

        $kernel = new CustomProjectDirKernel();
        $kernel->boot();

        $containerDir = __DIR__.'/Fixtures/var/cache/custom/'.substr(\get_class($kernel->getContainer()), 0, 16);
        $this->assertTrue(unlink(__DIR__.'/Fixtures/var/cache/custom/TestsSymfony_Component_HttpKernel_Tests_CustomProjectDirKernelCustomDebugContainer.php.meta'));
        $this->assertFileExists($containerDir);
        $this->assertFileNotExists($containerDir.'.legacy');

        $kernel = new CustomProjectDirKernel(function ($container) { $container->register('foo', 'stdClass')->setPublic(true); });
        $kernel->boot();

        $this->assertFileExists($containerDir);
        $this->assertFileExists($containerDir.'.legacy');

        $this->assertFileNotExists($legacyContainerDir);
        $this->assertFileNotExists($legacyContainerDir.'.legacy');
    }

    public function testBootInitializesBundlesAndContainer()
    {
        $kernel = $this->getKernel(array('initializeBundles', 'initializeContainer'));
        $kernel->expects($this->once())
            ->method('initializeBundles');
        $kernel->expects($this->once())
            ->method('initializeContainer');

        $kernel->boot();
    }

    public function testBootSetsTheContainerToTheBundles()
    {
        $bundle = $this->getMockBuilder('Symfony\Component\HttpKernel\Bundle\Bundle')->getMock();
        $bundle->expects($this->once())
            ->method('setContainer');

        $kernel = $this->getKernel(array('initializeBundles', 'initializeContainer', 'getBundles'));
        $kernel->expects($this->once())
            ->method('getBundles')
            ->will($this->returnValue(array($bundle)));

        $kernel->boot();
    }

    public function testBootSetsTheBootedFlagToTrue()
    {
        // use test kernel to access isBooted()
        $kernel = $this->getKernelForTest(array('initializeBundles', 'initializeContainer'));
        $kernel->boot();

        $this->assertTrue($kernel->isBooted());
    }

    public function testClassCacheIsNotLoadedByDefault()
    {
        $kernel = $this->getKernel(array('initializeBundles', 'initializeContainer', 'doLoadClassCache'));
        $kernel->expects($this->never())
            ->method('doLoadClassCache');

        $kernel->boot();
    }

    public function testBootKernelSeveralTimesOnlyInitializesBundlesOnce()
    {
        $kernel = $this->getKernel(array('initializeBundles', 'initializeContainer'));
        $kernel->expects($this->once())
            ->method('initializeBundles');

        $kernel->boot();
        $kernel->boot();
    }

    public function testShutdownCallsShutdownOnAllBundles()
    {
        $bundle = $this->getMockBuilder('Symfony\Component\HttpKernel\Bundle\Bundle')->getMock();
        $bundle->expects($this->once())
            ->method('shutdown');

        $kernel = $this->getKernel(array(), array($bundle));

        $kernel->boot();
        $kernel->shutdown();
    }

    public function testShutdownGivesNullContainerToAllBundles()
    {
        $bundle = $this->getMockBuilder('Symfony\Component\HttpKernel\Bundle\Bundle')->getMock();
        $bundle->expects($this->at(3))
            ->method('setContainer')
            ->with(null);

        $kernel = $this->getKernel(array('getBundles'));
        $kernel->expects($this->any())
            ->method('getBundles')
            ->will($this->returnValue(array($bundle)));

        $kernel->boot();
        $kernel->shutdown();
    }

    public function testHandleCallsHandleOnHttpKernel()
    {
        $type = HttpKernelInterface::MASTER_REQUEST;
        $catch = true;
        $request = new Request();

        $httpKernelMock = $this->getMockBuilder('Symfony\Component\HttpKernel\HttpKernel')
            ->disableOriginalConstructor()
            ->getMock();
        $httpKernelMock
            ->expects($this->once())
            ->method('handle')
            ->with($request, $type, $catch);

        $kernel = $this->getKernel(array('getHttpKernel'));
        $kernel->expects($this->once())
            ->method('getHttpKernel')
            ->will($this->returnValue($httpKernelMock));

        $kernel->handle($request, $type, $catch);
    }

    public function testHandleBootsTheKernel()
    {
        $type = HttpKernelInterface::MASTER_REQUEST;
        $catch = true;
        $request = new Request();

        $httpKernelMock = $this->getMockBuilder('Symfony\Component\HttpKernel\HttpKernel')
            ->disableOriginalConstructor()
            ->getMock();

        $kernel = $this->getKernel(array('getHttpKernel', 'boot'));
        $kernel->expects($this->once())
            ->method('getHttpKernel')
            ->will($this->returnValue($httpKernelMock));

        $kernel->expects($this->once())
            ->method('boot');

        $kernel->handle($request, $type, $catch);
    }

    public function testStripComments()
    {
        $source = <<<'EOF'
<?php

$string = 'string should not be   modified';

$string = 'string should not be

modified';


$heredoc = <<<HD


Heredoc should not be   modified {$a[1+$b]}


HD;

$nowdoc = <<<'ND'


Nowdoc should not be   modified


ND;

/**
 * some class comments to strip
 */
class TestClass
{
    /**
     * some method comments to strip
     */
    public function doStuff()
    {
        // inline comment
    }
}
EOF;
        $expected = <<<'EOF'
<?php
$string = 'string should not be   modified';
$string = 'string should not be

modified';
$heredoc = <<<HD


Heredoc should not be   modified {$a[1+$b]}


HD;
$nowdoc = <<<'ND'


Nowdoc should not be   modified


ND;
class TestClass
{
    public function doStuff()
    {
        }
}
EOF;

        $output = Kernel::stripComments($source);

        // Heredocs are preserved, making the output mixing Unix and Windows line
        // endings, switching to "\n" everywhere on Windows to avoid failure.
        if ('\\' === \DIRECTORY_SEPARATOR) {
            $expected = str_replace("\r\n", "\n", $expected);
            $output = str_replace("\r\n", "\n", $output);
        }

        $this->assertEquals($expected, $output);
    }

    /**
     * @group legacy
     */
    public function testGetRootDir()
    {
        $kernel = new KernelForTest('test', true);

        $this->assertEquals(__DIR__.\DIRECTORY_SEPARATOR.'Fixtures', realpath($kernel->getRootDir()));
    }

    /**
     * @group legacy
     */
    public function testGetName()
    {
        $kernel = new KernelForTest('test', true);

        $this->assertEquals('Fixtures', $kernel->getName());
    }

    /**
     * @group legacy
     */
    public function testOverrideGetName()
    {
        $kernel = new KernelForOverrideName('test', true);

        $this->assertEquals('overridden', $kernel->getName());
    }

    public function testSerialize()
    {
        $env = 'test_env';
        $debug = true;
        $kernel = new KernelForTest($env, $debug);

        $expected = serialize(array($env, $debug));
        $this->assertEquals($expected, $kernel->serialize());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testLocateResourceThrowsExceptionWhenNameIsNotValid()
    {
        $this->getKernel()->locateResource('Foo');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testLocateResourceThrowsExceptionWhenNameIsUnsafe()
    {
        $this->getKernel()->locateResource('@FooBundle/../bar');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testLocateResourceThrowsExceptionWhenBundleDoesNotExist()
    {
        $this->getKernel()->locateResource('@FooBundle/config/routing.xml');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testLocateResourceThrowsExceptionWhenResourceDoesNotExist()
    {
        $kernel = $this->getKernel(array('getBundle'));
        $kernel
            ->expects($this->once())
            ->method('getBundle')
            ->will($this->returnValue($this->getBundle(__DIR__.'/Fixtures/Bundle1Bundle')))
        ;

        $kernel->locateResource('@Bundle1Bundle/config/routing.xml');
    }

    public function testLocateResourceReturnsTheFirstThatMatches()
    {
        $kernel = $this->getKernel(array('getBundle'));
        $kernel
            ->expects($this->once())
            ->method('getBundle')
            ->will($this->returnValue($this->getBundle(__DIR__.'/Fixtures/Bundle1Bundle')))
        ;

        $this->assertEquals(__DIR__.'/Fixtures/Bundle1Bundle/foo.txt', $kernel->locateResource('@Bundle1Bundle/foo.txt'));
    }

    public function testLocateResourceIgnoresDirOnNonResource()
    {
        $kernel = $this->getKernel(array('getBundle'));
        $kernel
            ->expects($this->once())
            ->method('getBundle')
            ->will($this->returnValue($this->getBundle(__DIR__.'/Fixtures/Bundle1Bundle')))
        ;

        $this->assertEquals(
            __DIR__.'/Fixtures/Bundle1Bundle/foo.txt',
            $kernel->locateResource('@Bundle1Bundle/foo.txt', __DIR__.'/Fixtures')
        );
    }

    public function testLocateResourceReturnsTheDirOneForResources()
    {
        $kernel = $this->getKernel(array('getBundle'));
        $kernel
            ->expects($this->once())
            ->method('getBundle')
            ->will($this->returnValue($this->getBundle(__DIR__.'/Fixtures/FooBundle', null, null, 'FooBundle')))
        ;

        $this->assertEquals(
            __DIR__.'/Fixtures/Resources/FooBundle/foo.txt',
            $kernel->locateResource('@FooBundle/Resources/foo.txt', __DIR__.'/Fixtures/Resources')
        );
    }

    public function testLocateResourceOnDirectories()
    {
        $kernel = $this->getKernel(array('getBundle'));
        $kernel
            ->expects($this->exactly(2))
            ->method('getBundle')
            ->will($this->returnValue($this->getBundle(__DIR__.'/Fixtures/FooBundle', null, null, 'FooBundle')))
        ;

        $this->assertEquals(
            __DIR__.'/Fixtures/Resources/FooBundle/',
            $kernel->locateResource('@FooBundle/Resources/', __DIR__.'/Fixtures/Resources')
        );
        $this->assertEquals(
            __DIR__.'/Fixtures/Resources/FooBundle',
            $kernel->locateResource('@FooBundle/Resources', __DIR__.'/Fixtures/Resources')
        );

        $kernel = $this->getKernel(array('getBundle'));
        $kernel
            ->expects($this->exactly(2))
            ->method('getBundle')
            ->will($this->returnValue($this->getBundle(__DIR__.'/Fixtures/Bundle1Bundle', null, null, 'Bundle1Bundle')))
        ;

        $this->assertEquals(
            __DIR__.'/Fixtures/Bundle1Bundle/Resources/',
            $kernel->locateResource('@Bundle1Bundle/Resources/')
        );
        $this->assertEquals(
            __DIR__.'/Fixtures/Bundle1Bundle/Resources',
            $kernel->locateResource('@Bundle1Bundle/Resources')
        );
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Trying to register two bundles with the same name "DuplicateName"
     */
    public function testInitializeBundleThrowsExceptionWhenRegisteringTwoBundlesWithTheSameName()
    {
        $fooBundle = $this->getBundle(null, null, 'FooBundle', 'DuplicateName');
        $barBundle = $this->getBundle(null, null, 'BarBundle', 'DuplicateName');

        $kernel = $this->getKernel(array(), array($fooBundle, $barBundle));
        $kernel->boot();
    }

    public function testTerminateReturnsSilentlyIfKernelIsNotBooted()
    {
        $kernel = $this->getKernel(array('getHttpKernel'));
        $kernel->expects($this->never())
            ->method('getHttpKernel');

        $kernel->terminate(Request::create('/'), new Response());
    }

    public function testTerminateDelegatesTerminationOnlyForTerminableInterface()
    {
        // does not implement TerminableInterface
        $httpKernel = new TestKernel();

        $kernel = $this->getKernel(array('getHttpKernel'));
        $kernel->expects($this->once())
            ->method('getHttpKernel')
            ->willReturn($httpKernel);

        $kernel->boot();
        $kernel->terminate(Request::create('/'), new Response());

        $this->assertFalse($httpKernel->terminateCalled, 'terminate() is never called if the kernel class does not implement TerminableInterface');

        // implements TerminableInterface
        $httpKernelMock = $this->getMockBuilder('Symfony\Component\HttpKernel\HttpKernel')
            ->disableOriginalConstructor()
            ->setMethods(array('terminate'))
            ->getMock();

        $httpKernelMock
            ->expects($this->once())
            ->method('terminate');

        $kernel = $this->getKernel(array('getHttpKernel'));
        $kernel->expects($this->exactly(2))
            ->method('getHttpKernel')
            ->will($this->returnValue($httpKernelMock));

        $kernel->boot();
        $kernel->terminate(Request::create('/'), new Response());
    }

    public function testKernelWithoutBundles()
    {
        $kernel = new KernelWithoutBundles('test', true);
        $kernel->boot();

        $this->assertTrue($kernel->getContainer()->getParameter('test_executed'));
    }

    public function testProjectDirExtension()
    {
        $kernel = new CustomProjectDirKernel();
        $kernel->boot();

        $this->assertSame(__DIR__.'/Fixtures', $kernel->getProjectDir());
        $this->assertSame(__DIR__.\DIRECTORY_SEPARATOR.'Fixtures', $kernel->getContainer()->getParameter('kernel.project_dir'));
    }

    public function testKernelReset()
    {
        (new Filesystem())->remove(__DIR__.'/Fixtures/var/cache');

        $kernel = new CustomProjectDirKernel();
        $kernel->boot();

        $containerClass = \get_class($kernel->getContainer());
        $containerFile = (new \ReflectionClass($kernel->getContainer()))->getFileName();
        unlink(__DIR__.'/Fixtures/var/cache/custom/TestsSymfony_Component_HttpKernel_Tests_CustomProjectDirKernelCustomDebugContainer.php.meta');

        $kernel = new CustomProjectDirKernel();
        $kernel->boot();

        $this->assertInstanceOf($containerClass, $kernel->getContainer());
        $this->assertFileExists($containerFile);
        unlink(__DIR__.'/Fixtures/var/cache/custom/TestsSymfony_Component_HttpKernel_Tests_CustomProjectDirKernelCustomDebugContainer.php.meta');

        $kernel = new CustomProjectDirKernel(function ($container) { $container->register('foo', 'stdClass')->setPublic(true); });
        $kernel->boot();

        $this->assertNotInstanceOf($containerClass, $kernel->getContainer());
        $this->assertFileExists($containerFile);
        $this->assertFileExists(\dirname($containerFile).'.legacy');
    }

    public function testKernelPass()
    {
        $kernel = new PassKernel();
        $kernel->boot();

        $this->assertTrue($kernel->getContainer()->getParameter('test.processed'));
    }

    public function testServicesResetter()
    {
        $httpKernelMock = $this->getMockBuilder(HttpKernelInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $httpKernelMock
            ->expects($this->exactly(2))
            ->method('handle');

        $kernel = new CustomProjectDirKernel(function ($container) {
            $container->addCompilerPass(new ResettableServicePass());
            $container->register('one', ResettableService::class)
                ->setPublic(true)
                ->addTag('kernel.reset', array('method' => 'reset'));
            $container->register('services_resetter', ServicesResetter::class)->setPublic(true);
        }, $httpKernelMock, 'resetting');

        ResettableService::$counter = 0;

        $request = new Request();

        $kernel->handle($request);
        $kernel->getContainer()->get('one');

        $this->assertEquals(0, ResettableService::$counter);
        $this->assertFalse($kernel->getContainer()->initialized('services_resetter'));

        $kernel->handle($request);

        $this->assertEquals(1, ResettableService::$counter);
    }

    /**
     * @group time-sensitive
     */
    public function testKernelStartTimeIsResetWhileBootingAlreadyBootedKernel()
    {
        $kernel = $this->getKernelForTest(array('initializeBundles'), true);
        $kernel->boot();
        $preReBoot = $kernel->getStartTime();

        sleep(3600); //Intentionally large value to detect if ClockMock ever breaks
        $kernel->reboot(null);

        $this->assertGreaterThan($preReBoot, $kernel->getStartTime());
    }

    /**
     * Returns a mock for the BundleInterface.
     *
     * @return BundleInterface
     */
    protected function getBundle($dir = null, $parent = null, $className = null, $bundleName = null)
    {
        $bundle = $this
            ->getMockBuilder('Symfony\Component\HttpKernel\Bundle\BundleInterface')
            ->setMethods(array('getPath', 'getParent', 'getName'))
            ->disableOriginalConstructor()
        ;

        if ($className) {
            $bundle->setMockClassName($className);
        }

        $bundle = $bundle->getMockForAbstractClass();

        $bundle
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue(null === $bundleName ? \get_class($bundle) : $bundleName))
        ;

        $bundle
            ->expects($this->any())
            ->method('getPath')
            ->will($this->returnValue($dir))
        ;

        $bundle
            ->expects($this->any())
            ->method('getParent')
            ->will($this->returnValue($parent))
        ;

        return $bundle;
    }

    /**
     * Returns a mock for the abstract kernel.
     *
     * @param array $methods Additional methods to mock (besides the abstract ones)
     * @param array $bundles Bundles to register
     *
     * @return Kernel
     */
    protected function getKernel(array $methods = array(), array $bundles = array())
    {
        $methods[] = 'registerBundles';

        $kernel = $this
            ->getMockBuilder('Symfony\Component\HttpKernel\Kernel')
            ->setMethods($methods)
            ->setConstructorArgs(array('test', false))
            ->getMockForAbstractClass()
        ;
        $kernel->expects($this->any())
            ->method('registerBundles')
            ->will($this->returnValue($bundles))
        ;
        $p = new \ReflectionProperty($kernel, 'rootDir');
        $p->setAccessible(true);
        $p->setValue($kernel, __DIR__.'/Fixtures');

        return $kernel;
    }

    protected function getKernelForTest(array $methods = array(), $debug = false)
    {
        $kernel = $this->getMockBuilder('Symfony\Component\HttpKernel\Tests\Fixtures\KernelForTest')
            ->setConstructorArgs(array('test', $debug))
            ->setMethods($methods)
            ->getMock();
        $p = new \ReflectionProperty($kernel, 'rootDir');
        $p->setAccessible(true);
        $p->setValue($kernel, __DIR__.'/Fixtures');

        return $kernel;
    }
}

class TestKernel implements HttpKernelInterface
{
    public $terminateCalled = false;

    public function terminate()
    {
        $this->terminateCalled = true;
    }

    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true)
    {
    }
}

class CustomProjectDirKernel extends Kernel
{
    private $baseDir;
    private $buildContainer;
    private $httpKernel;

    public function __construct(\Closure $buildContainer = null, HttpKernelInterface $httpKernel = null, $env = 'custom')
    {
        parent::__construct($env, true);

        $this->buildContainer = $buildContainer;
        $this->httpKernel = $httpKernel;
    }

    public function registerBundles()
    {
        return array();
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
    }

    public function getProjectDir()
    {
        return __DIR__.'/Fixtures';
    }

    protected function build(ContainerBuilder $container)
    {
        if ($build = $this->buildContainer) {
            $build($container);
        }
    }

    protected function getHttpKernel()
    {
        return $this->httpKernel;
    }
}

class PassKernel extends CustomProjectDirKernel implements CompilerPassInterface
{
    public function __construct()
    {
        parent::__construct();
        Kernel::__construct('pass', true);
    }

    public function process(ContainerBuilder $container)
    {
        $container->setParameter('test.processed', true);
    }
}
