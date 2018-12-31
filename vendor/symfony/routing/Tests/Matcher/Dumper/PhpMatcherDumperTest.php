<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Routing\Tests\Matcher\Dumper;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Matcher\Dumper\PhpMatcherDumper;
use Symfony\Component\Routing\Matcher\RedirectableUrlMatcherInterface;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class PhpMatcherDumperTest extends TestCase
{
    /**
     * @var string
     */
    private $matcherClass;

    /**
     * @var string
     */
    private $dumpPath;

    protected function setUp()
    {
        parent::setUp();

        $this->matcherClass = uniqid('ProjectUrlMatcher');
        $this->dumpPath = sys_get_temp_dir().\DIRECTORY_SEPARATOR.'php_matcher.'.$this->matcherClass.'.php';
    }

    protected function tearDown()
    {
        parent::tearDown();

        @unlink($this->dumpPath);
    }

    public function testRedirectPreservesUrlEncoding()
    {
        $collection = new RouteCollection();
        $collection->add('foo', new Route('/foo:bar/'));

        $class = $this->generateDumpedMatcher($collection, true);

        $matcher = $this->getMockBuilder($class)
                        ->setMethods(array('redirect'))
                        ->setConstructorArgs(array(new RequestContext()))
                        ->getMock();

        $matcher->expects($this->once())->method('redirect')->with('/foo%3Abar/', 'foo')->willReturn(array());

        $matcher->match('/foo%3Abar');
    }

    /**
     * @dataProvider getRouteCollections
     */
    public function testDump(RouteCollection $collection, $fixture, $options = array())
    {
        $basePath = __DIR__.'/../../Fixtures/dumper/';

        $dumper = new PhpMatcherDumper($collection);
        $this->assertStringEqualsFile($basePath.$fixture, $dumper->dump($options), '->dump() correctly dumps routes as optimized PHP code.');
    }

    public function getRouteCollections()
    {
        /* test case 1 */

        $collection = new RouteCollection();

        $collection->add('overridden', new Route('/overridden'));

        // defaults and requirements
        $collection->add('foo', new Route(
            '/foo/{bar}',
            array('def' => 'test'),
            array('bar' => 'baz|symfony')
        ));
        // method requirement
        $collection->add('bar', new Route(
            '/bar/{foo}',
            array(),
            array(),
            array(),
            '',
            array(),
            array('GET', 'head')
        ));
        // GET method requirement automatically adds HEAD as valid
        $collection->add('barhead', new Route(
            '/barhead/{foo}',
            array(),
            array(),
            array(),
            '',
            array(),
            array('GET')
        ));
        // simple
        $collection->add('baz', new Route(
            '/test/baz'
        ));
        // simple with extension
        $collection->add('baz2', new Route(
            '/test/baz.html'
        ));
        // trailing slash
        $collection->add('baz3', new Route(
            '/test/baz3/'
        ));
        // trailing slash with variable
        $collection->add('baz4', new Route(
            '/test/{foo}/'
        ));
        // trailing slash and method
        $collection->add('baz5', new Route(
            '/test/{foo}/',
            array(),
            array(),
            array(),
            '',
            array(),
            array('post')
        ));
        // complex name
        $collection->add('baz.baz6', new Route(
            '/test/{foo}/',
            array(),
            array(),
            array(),
            '',
            array(),
            array('put')
        ));
        // defaults without variable
        $collection->add('foofoo', new Route(
            '/foofoo',
            array('def' => 'test')
        ));
        // pattern with quotes
        $collection->add('quoter', new Route(
            '/{quoter}',
            array(),
            array('quoter' => '[\']+')
        ));
        // space in pattern
        $collection->add('space', new Route(
            '/spa ce'
        ));

        // prefixes
        $collection1 = new RouteCollection();
        $collection1->add('overridden', new Route('/overridden1'));
        $collection1->add('foo1', (new Route('/{foo}'))->setMethods('PUT'));
        $collection1->add('bar1', new Route('/{bar}'));
        $collection1->addPrefix('/b\'b');
        $collection2 = new RouteCollection();
        $collection2->addCollection($collection1);
        $collection2->add('overridden', new Route('/{var}', array(), array('var' => '.*')));
        $collection1 = new RouteCollection();
        $collection1->add('foo2', new Route('/{foo1}'));
        $collection1->add('bar2', new Route('/{bar1}'));
        $collection1->addPrefix('/b\'b');
        $collection2->addCollection($collection1);
        $collection2->addPrefix('/a');
        $collection->addCollection($collection2);

        // overridden through addCollection() and multiple sub-collections with no own prefix
        $collection1 = new RouteCollection();
        $collection1->add('overridden2', new Route('/old'));
        $collection1->add('helloWorld', new Route('/hello/{who}', array('who' => 'World!')));
        $collection2 = new RouteCollection();
        $collection3 = new RouteCollection();
        $collection3->add('overridden2', new Route('/new'));
        $collection3->add('hey', new Route('/hey/'));
        $collection2->addCollection($collection3);
        $collection1->addCollection($collection2);
        $collection1->addPrefix('/multi');
        $collection->addCollection($collection1);

        // "dynamic" prefix
        $collection1 = new RouteCollection();
        $collection1->add('foo3', new Route('/{foo}'));
        $collection1->add('bar3', new Route('/{bar}'));
        $collection1->addPrefix('/b');
        $collection1->addPrefix('{_locale}');
        $collection->addCollection($collection1);

        // route between collections
        $collection->add('ababa', new Route('/ababa'));

        // collection with static prefix but only one route
        $collection1 = new RouteCollection();
        $collection1->add('foo4', new Route('/{foo}'));
        $collection1->addPrefix('/aba');
        $collection->addCollection($collection1);

        // prefix and host

        $collection1 = new RouteCollection();

        $route1 = new Route('/route1', array(), array(), array(), 'a.example.com');
        $collection1->add('route1', $route1);

        $route2 = new Route('/c2/route2', array(), array(), array(), 'a.example.com');
        $collection1->add('route2', $route2);

        $route3 = new Route('/c2/route3', array(), array(), array(), 'b.example.com');
        $collection1->add('route3', $route3);

        $route4 = new Route('/route4', array(), array(), array(), 'a.example.com');
        $collection1->add('route4', $route4);

        $route5 = new Route('/route5', array(), array(), array(), 'c.example.com');
        $collection1->add('route5', $route5);

        $route6 = new Route('/route6', array(), array(), array(), null);
        $collection1->add('route6', $route6);

        $collection->addCollection($collection1);

        // host and variables

        $collection1 = new RouteCollection();

        $route11 = new Route('/route11', array(), array(), array(), '{var1}.example.com');
        $collection1->add('route11', $route11);

        $route12 = new Route('/route12', array('var1' => 'val'), array(), array(), '{var1}.example.com');
        $collection1->add('route12', $route12);

        $route13 = new Route('/route13/{name}', array(), array(), array(), '{var1}.example.com');
        $collection1->add('route13', $route13);

        $route14 = new Route('/route14/{name}', array('var1' => 'val'), array(), array(), '{var1}.example.com');
        $collection1->add('route14', $route14);

        $route15 = new Route('/route15/{name}', array(), array(), array(), 'c.example.com');
        $collection1->add('route15', $route15);

        $route16 = new Route('/route16/{name}', array('var1' => 'val'), array(), array(), null);
        $collection1->add('route16', $route16);

        $route17 = new Route('/route17', array(), array(), array(), null);
        $collection1->add('route17', $route17);

        $collection->addCollection($collection1);

        // multiple sub-collections with a single route and a prefix each
        $collection1 = new RouteCollection();
        $collection1->add('a', new Route('/a...'));
        $collection2 = new RouteCollection();
        $collection2->add('b', new Route('/{var}'));
        $collection3 = new RouteCollection();
        $collection3->add('c', new Route('/{var}'));
        $collection3->addPrefix('/c');
        $collection2->addCollection($collection3);
        $collection2->addPrefix('/b');
        $collection1->addCollection($collection2);
        $collection1->addPrefix('/a');
        $collection->addCollection($collection1);

        /* test case 2 */

        $redirectCollection = clone $collection;

        // force HTTPS redirection
        $redirectCollection->add('secure', new Route(
            '/secure',
            array(),
            array(),
            array(),
            '',
            array('https')
        ));

        // force HTTP redirection
        $redirectCollection->add('nonsecure', new Route(
            '/nonsecure',
            array(),
            array(),
            array(),
            '',
            array('http')
        ));

        /* test case 3 */

        $rootprefixCollection = new RouteCollection();
        $rootprefixCollection->add('static', new Route('/test'));
        $rootprefixCollection->add('dynamic', new Route('/{var}'));
        $rootprefixCollection->addPrefix('rootprefix');
        $route = new Route('/with-condition');
        $route->setCondition('context.getMethod() == "GET"');
        $rootprefixCollection->add('with-condition', $route);

        /* test case 4 */
        $headMatchCasesCollection = new RouteCollection();
        $headMatchCasesCollection->add('just_head', new Route(
            '/just_head',
            array(),
            array(),
            array(),
            '',
            array(),
            array('HEAD')
        ));
        $headMatchCasesCollection->add('head_and_get', new Route(
            '/head_and_get',
            array(),
            array(),
            array(),
            '',
            array(),
            array('HEAD', 'GET')
        ));
        $headMatchCasesCollection->add('get_and_head', new Route(
            '/get_and_head',
            array(),
            array(),
            array(),
            '',
            array(),
            array('GET', 'HEAD')
        ));
        $headMatchCasesCollection->add('post_and_head', new Route(
            '/post_and_head',
            array(),
            array(),
            array(),
            '',
            array(),
            array('POST', 'HEAD')
        ));
        $headMatchCasesCollection->add('put_and_post', new Route(
            '/put_and_post',
            array(),
            array(),
            array(),
            '',
            array(),
            array('PUT', 'POST')
        ));
        $headMatchCasesCollection->add('put_and_get_and_head', new Route(
            '/put_and_post',
            array(),
            array(),
            array(),
            '',
            array(),
            array('PUT', 'GET', 'HEAD')
        ));

        /* test case 5 */
        $groupOptimisedCollection = new RouteCollection();
        $groupOptimisedCollection->add('a_first', new Route('/a/11'));
        $groupOptimisedCollection->add('a_second', new Route('/a/22'));
        $groupOptimisedCollection->add('a_third', new Route('/a/333'));
        $groupOptimisedCollection->add('a_wildcard', new Route('/{param}'));
        $groupOptimisedCollection->add('a_fourth', new Route('/a/44/'));
        $groupOptimisedCollection->add('a_fifth', new Route('/a/55/'));
        $groupOptimisedCollection->add('a_sixth', new Route('/a/66/'));
        $groupOptimisedCollection->add('nested_wildcard', new Route('/nested/{param}'));
        $groupOptimisedCollection->add('nested_a', new Route('/nested/group/a/'));
        $groupOptimisedCollection->add('nested_b', new Route('/nested/group/b/'));
        $groupOptimisedCollection->add('nested_c', new Route('/nested/group/c/'));

        $groupOptimisedCollection->add('slashed_a', new Route('/slashed/group/'));
        $groupOptimisedCollection->add('slashed_b', new Route('/slashed/group/b/'));
        $groupOptimisedCollection->add('slashed_c', new Route('/slashed/group/c/'));

        /* test case 6 & 7 */
        $trailingSlashCollection = new RouteCollection();
        $trailingSlashCollection->add('simple_trailing_slash_no_methods', new Route('/trailing/simple/no-methods/', array(), array(), array(), '', array(), array()));
        $trailingSlashCollection->add('simple_trailing_slash_GET_method', new Route('/trailing/simple/get-method/', array(), array(), array(), '', array(), array('GET')));
        $trailingSlashCollection->add('simple_trailing_slash_HEAD_method', new Route('/trailing/simple/head-method/', array(), array(), array(), '', array(), array('HEAD')));
        $trailingSlashCollection->add('simple_trailing_slash_POST_method', new Route('/trailing/simple/post-method/', array(), array(), array(), '', array(), array('POST')));
        $trailingSlashCollection->add('regex_trailing_slash_no_methods', new Route('/trailing/regex/no-methods/{param}/', array(), array(), array(), '', array(), array()));
        $trailingSlashCollection->add('regex_trailing_slash_GET_method', new Route('/trailing/regex/get-method/{param}/', array(), array(), array(), '', array(), array('GET')));
        $trailingSlashCollection->add('regex_trailing_slash_HEAD_method', new Route('/trailing/regex/head-method/{param}/', array(), array(), array(), '', array(), array('HEAD')));
        $trailingSlashCollection->add('regex_trailing_slash_POST_method', new Route('/trailing/regex/post-method/{param}/', array(), array(), array(), '', array(), array('POST')));

        $trailingSlashCollection->add('simple_not_trailing_slash_no_methods', new Route('/not-trailing/simple/no-methods', array(), array(), array(), '', array(), array()));
        $trailingSlashCollection->add('simple_not_trailing_slash_GET_method', new Route('/not-trailing/simple/get-method', array(), array(), array(), '', array(), array('GET')));
        $trailingSlashCollection->add('simple_not_trailing_slash_HEAD_method', new Route('/not-trailing/simple/head-method', array(), array(), array(), '', array(), array('HEAD')));
        $trailingSlashCollection->add('simple_not_trailing_slash_POST_method', new Route('/not-trailing/simple/post-method', array(), array(), array(), '', array(), array('POST')));
        $trailingSlashCollection->add('regex_not_trailing_slash_no_methods', new Route('/not-trailing/regex/no-methods/{param}', array(), array(), array(), '', array(), array()));
        $trailingSlashCollection->add('regex_not_trailing_slash_GET_method', new Route('/not-trailing/regex/get-method/{param}', array(), array(), array(), '', array(), array('GET')));
        $trailingSlashCollection->add('regex_not_trailing_slash_HEAD_method', new Route('/not-trailing/regex/head-method/{param}', array(), array(), array(), '', array(), array('HEAD')));
        $trailingSlashCollection->add('regex_not_trailing_slash_POST_method', new Route('/not-trailing/regex/post-method/{param}', array(), array(), array(), '', array(), array('POST')));

        /* test case 8 */
        $unicodeCollection = new RouteCollection();
        $unicodeCollection->add('a', new Route('/{a}', array(), array('a' => 'a'), array('utf8' => false)));
        $unicodeCollection->add('b', new Route('/{a}', array(), array('a' => '.'), array('utf8' => true)));
        $unicodeCollection->add('c', new Route('/{a}', array(), array('a' => '.'), array('utf8' => false)));

        /* test case 9 */
        $hostTreeCollection = new RouteCollection();
        $hostTreeCollection->add('a', (new Route('/'))->setHost('{d}.e.c.b.a'));
        $hostTreeCollection->add('b', (new Route('/'))->setHost('d.c.b.a'));
        $hostTreeCollection->add('c', (new Route('/'))->setHost('{e}.e.c.b.a'));

        /* test case 10 */
        $chunkedCollection = new RouteCollection();
        for ($i = 0; $i < 1000; ++$i) {
            $h = substr(md5($i), 0, 6);
            $chunkedCollection->add('_'.$i, new Route('/'.$h.'/{a}/{b}/{c}/'.$h));
        }

        /* test case 11 */
        $demoCollection = new RouteCollection();
        $demoCollection->add('a', new Route('/admin/post/'));
        $demoCollection->add('b', new Route('/admin/post/new'));
        $demoCollection->add('c', (new Route('/admin/post/{id}'))->setRequirements(array('id' => '\d+')));
        $demoCollection->add('d', (new Route('/admin/post/{id}/edit'))->setRequirements(array('id' => '\d+')));
        $demoCollection->add('e', (new Route('/admin/post/{id}/delete'))->setRequirements(array('id' => '\d+')));
        $demoCollection->add('f', new Route('/blog/'));
        $demoCollection->add('g', new Route('/blog/rss.xml'));
        $demoCollection->add('h', (new Route('/blog/page/{page}'))->setRequirements(array('id' => '\d+')));
        $demoCollection->add('i', (new Route('/blog/posts/{page}'))->setRequirements(array('id' => '\d+')));
        $demoCollection->add('j', (new Route('/blog/comments/{id}/new'))->setRequirements(array('id' => '\d+')));
        $demoCollection->add('k', new Route('/blog/search'));
        $demoCollection->add('l', new Route('/login'));
        $demoCollection->add('m', new Route('/logout'));
        $demoCollection->addPrefix('/{_locale}');
        $demoCollection->add('n', new Route('/{_locale}'));
        $demoCollection->addRequirements(array('_locale' => 'en|fr'));
        $demoCollection->addDefaults(array('_locale' => 'en'));

        /* test case 12 */
        $suffixCollection = new RouteCollection();
        $suffixCollection->add('r1', new Route('abc{foo}/1'));
        $suffixCollection->add('r2', new Route('abc{foo}/2'));
        $suffixCollection->add('r10', new Route('abc{foo}/10'));
        $suffixCollection->add('r20', new Route('abc{foo}/20'));
        $suffixCollection->add('r100', new Route('abc{foo}/100'));
        $suffixCollection->add('r200', new Route('abc{foo}/200'));

        /* test case 13 */
        $hostCollection = new RouteCollection();
        $hostCollection->add('r1', (new Route('abc{foo}'))->setHost('{foo}.exampple.com'));
        $hostCollection->add('r2', (new Route('abc{foo}'))->setHost('{foo}.exampple.com'));

        return array(
           array(new RouteCollection(), 'url_matcher0.php', array()),
           array($collection, 'url_matcher1.php', array()),
           array($redirectCollection, 'url_matcher2.php', array('base_class' => 'Symfony\Component\Routing\Tests\Fixtures\RedirectableUrlMatcher')),
           array($rootprefixCollection, 'url_matcher3.php', array()),
           array($headMatchCasesCollection, 'url_matcher4.php', array()),
           array($groupOptimisedCollection, 'url_matcher5.php', array('base_class' => 'Symfony\Component\Routing\Tests\Fixtures\RedirectableUrlMatcher')),
           array($trailingSlashCollection, 'url_matcher6.php', array()),
           array($trailingSlashCollection, 'url_matcher7.php', array('base_class' => 'Symfony\Component\Routing\Tests\Fixtures\RedirectableUrlMatcher')),
           array($unicodeCollection, 'url_matcher8.php', array()),
           array($hostTreeCollection, 'url_matcher9.php', array()),
           array($chunkedCollection, 'url_matcher10.php', array()),
           array($demoCollection, 'url_matcher11.php', array('base_class' => 'Symfony\Component\Routing\Tests\Fixtures\RedirectableUrlMatcher')),
           array($suffixCollection, 'url_matcher12.php', array()),
           array($hostCollection, 'url_matcher13.php', array()),
        );
    }

    private function generateDumpedMatcher(RouteCollection $collection, $redirectableStub = false)
    {
        $options = array('class' => $this->matcherClass);

        if ($redirectableStub) {
            $options['base_class'] = '\Symfony\Component\Routing\Tests\Matcher\Dumper\RedirectableUrlMatcherStub';
        }

        $dumper = new PhpMatcherDumper($collection);
        $code = $dumper->dump($options);

        file_put_contents($this->dumpPath, $code);
        include $this->dumpPath;

        return $this->matcherClass;
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Symfony\Component\Routing\Route cannot contain objects
     */
    public function testGenerateDumperMatcherWithObject()
    {
        $routeCollection = new RouteCollection();
        $routeCollection->add('_', new Route('/', array(new \stdClass())));
        $dumper = new PhpMatcherDumper($routeCollection);
        $dumper->dump();
    }
}

abstract class RedirectableUrlMatcherStub extends UrlMatcher implements RedirectableUrlMatcherInterface
{
    public function redirect($path, $route, $scheme = null)
    {
    }
}
