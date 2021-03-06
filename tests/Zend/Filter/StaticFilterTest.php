<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Filter;

use Zend\Filter\StaticFilter,
    Zend\Loader\PluginLoader;

/**
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Filter
 */
class StaticFilterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Resets the default namespaces
     *
     * @return void
     */
    public function tearDown()
    {
        StaticFilter::setPluginLoader(null);
    }

    public function testPluginLoaderInstanceWithZendFilterPathsRegisteredByDefault()
    {
        $loader = StaticFilter::getPluginLoader();
        $this->assertType('Zend\Loader\PluginLoader', $loader);
        $paths = $loader->getPaths('Zend\Filter');
        $this->assertEquals(1, count($paths));
    }

    public function testCanSpecifyCustomPluginLoader()
    {
        $loader = new PluginLoader();
        StaticFilter::setPluginLoader($loader);
        $this->assertSame($loader, StaticFilter::getPluginLoader());
    }

    public function testCanResetPluginLoaderByPassingNull()
    {
        $loader = new PluginLoader();
        StaticFilter::setPluginLoader($loader);
        $this->assertSame($loader, StaticFilter::getPluginLoader());
        StaticFilter::setPluginLoader(null);
        $registered = StaticFilter::getPluginLoader();
        $this->assertNotSame($loader, $registered);
        $this->assertType('Zend\Loader\PluginLoader', $registered);
    }

    /**
     * Ensures that we can call the static method execute()
     * to instantiate a named validator by its class basename
     * and it returns the result of filter() with the input.
     */
    public function testStaticFactory()
    {
        $filteredValue = StaticFilter::execute('1a2b3c4d', 'Digits');
        $this->assertEquals('1234', $filteredValue);
    }

    /**
     * Ensures that a validator with constructor arguments can be called
     * with the static method get().
     */
    public function testStaticFactoryWithConstructorArguments()
    {
        // Test HtmlEntities with one ctor argument.
        $filteredValue = StaticFilter::execute('"O\'Reilly"', 'HtmlEntities', array(array('quotestyle' => ENT_COMPAT)));
        $this->assertEquals('&quot;O\'Reilly&quot;', $filteredValue);

        // Test HtmlEntities with a different ctor argument,
        // and make sure it gives the correct response
        // so we know it passed the arg to the ctor.
        $filteredValue = StaticFilter::execute('"O\'Reilly"', 'HtmlEntities', array(array('quotestyle' => ENT_QUOTES)));
        $this->assertEquals('&quot;O&#039;Reilly&quot;', $filteredValue);
    }

    /**
     * Ensures that if we specify a validator class basename that doesn't
     * exist in the namespace, get() throws an exception.
     *
     * Refactored to conform with ZF-2724.
     *
     * @group  ZF-2724
     */
    public function testStaticFactoryClassNotFound()
    {
        $this->setExpectedException('\\Zend\\Filter\\Exception', 'not found');
        StaticFilter::execute('1234', 'UnknownFilter');
    }
}
