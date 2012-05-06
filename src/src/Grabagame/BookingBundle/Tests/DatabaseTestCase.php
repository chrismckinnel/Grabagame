<?php

namespace Grabagame\BookingBundle\Tests;
require_once('PHPUnit/Autoload.php');

use Doctrine\ORM\Tools\SchemaTool;
use DoctrineExtensions\PHPUnit\OrmTestCase;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Doctrine\ORM\EntityManager;

abstract class DatabaseTestCase extends OrmTestCase
{
    static protected $class;
    static protected $kernel;

    /**
     * Finds the directory where the phpunit.xml(.dist) is stored.
     *
     * If you run tests with the PHPUnit CLI tool, everything will work as expected.
     * If not, override this method in your test classes.
     *
     * @return string The directory where phpunit.xml(.dist) is stored
     */
    static protected function getPhpUnitXmlDir()
    {
        if (!isset($_SERVER['argv']) || false === strpos($_SERVER['argv'][0], 'phpunit')) {
            throw new \RuntimeException('You must override the WebTestCase::createKernel() method.');
        }

        $dir = static::getPhpUnitCliConfigArgument();
        if ($dir === null &&
            (file_exists(getcwd().DIRECTORY_SEPARATOR.'phpunit.xml') ||
            file_exists(getcwd().DIRECTORY_SEPARATOR.'phpunit.xml.dist'))) {
            $dir = getcwd();
        }

        // Can't continue
        if ($dir === null) {
            throw new \RuntimeException('Unable to guess the Kernel directory.');
        }

        if (!is_dir($dir)) {
            $dir = dirname($dir);
        }

        return $dir;
    }

    /**
     * Finds the value of configuration flag from cli
     *
     * PHPUnit will use the last configuration argument on the command line, so this only returns
     * the last configuration argument
     *
     * @return string The value of the phpunit cli configuration option
     */
    static private function getPhpUnitCliConfigArgument()
    {
        $dir = null;
        $reversedArgs = array_reverse($_SERVER['argv']);
        foreach ($reversedArgs as $argIndex => $testArg) {
            if ($testArg === '-c' || $testArg === '--configuration') {
                $dir = realpath($reversedArgs[$argIndex - 1]);
                break;
            } elseif (strpos($testArg, '--configuration=') === 0) {
                $argPath = substr($testArg, strlen('--configuration='));
                $dir = realpath($argPath);
                break;
            }
        }

        return $dir;
    }

    /**
     * Attempts to guess the kernel location.
     *
     * When the Kernel is located, the file is required.
     *
     * @return string The Kernel class name
     */
    static protected function getKernelClass()
    {
        $dir = isset($_SERVER['KERNEL_DIR']) ? $_SERVER['KERNEL_DIR'] : static::getPhpUnitXmlDir();

        $finder = new Finder();
        $finder->name('*Kernel.php')->depth(0)->in($dir);
        $results = iterator_to_array($finder);
        if (!count($results)) {
            throw new \RuntimeException('Either set KERNEL_DIR in your phpunit.xml according to http://symfony.com/doc/current/book/testing.html#your-first-functional-test or override the WebTestCase::createKernel() method.');
        }

        $file = current($results);
        $class = $file->getBasename('.php');

        require_once $file;

        return $class;
    }

    /**
     * Creates a Kernel.
     *
     * Available options:
     *
     *  * environment
     *  * debug
     *
     * @param array $options An array of options
     *
     * @return HttpKernelInterface A HttpKernelInterface instance
     */
    static protected function createKernel(array $options = array())
    {
        if (null === static::$class) {
            static::$class = static::getKernelClass();
        }

        return new static::$class(
            isset($options['environment']) ? $options['environment'] : 'test',
            isset($options['debug']) ? $options['debug'] : true
        );
    }

    /**
     * Shuts the kernel down if it was used in the test.
     */
    protected function tearDown()
    {
        parent::tearDown();
        static::$kernel->getContainer()->get('doctrine')->resetEntityManager();
        if (null !== static::$kernel) {
            static::$kernel->shutdown();
            static::$kernel = null;
        }
    }

    /**
     * @return EntityManager
     */
    protected function createEntityManager()
    {
        if (null == static::$kernel) {
            static::$kernel = static::createKernel(array(
                'environment' => 'test',
                'debug' => true
            ));
            static::$kernel->boot();
        }

        $em = static::$kernel->getContainer()->get('doctrine')->getEntityManager();
        $em->getEventManager()->addEventListener(array("preTestSetUp"), new SchemaSetupListener());
        $em->getConfiguration()->setEntityNamespaces(array(
            'GrabagameBookingBundle' => 'Grabagame\\BookingBundle\\Entity'
        ));

        return $em;
    }

    /**
     * Creates a yaml dataset from the given array
     * of yaml files. A single filename in a string
     * can also be supplied.
     *
     * All yaml files should be stored in the _fixtures
     * directory (which resides in the same directory
     * as this class).
     *
     * IMPORTANT: the yaml files should be supplied in
     * order otherwise you may experience foreign key
     * constraint violations
     *
     * @param mixed $yamlFiles
     *
     * @return \PHPUnit_Extensions_Database_DataSet_YamlDataSet
     */
    public function getYamlDataSet($yamlFiles)
    {
        if (!is_array($yamlFiles))
            $yamlFiles = array($yamlFiles);

        $first = __DIR__ . "/_fixtures/" . array_shift($yamlFiles);

        $ds = new \PHPUnit_Extensions_Database_DataSet_YamlDataSet($first);

        foreach ($yamlFiles as $file) {
            $ds->addYamlFile(__DIR__ . "/_fixtures/" . $file);
        }

        return $ds;
    }

    public function getCsvDataSet($csvFiles)
    {
        $dataSet = new \PHPUnit_Extensions_Database_DataSet_CsvDataSet();

        foreach ($csvFiles as $table => $file) {
            $dataSet->addTable($table, __DIR__ . "/_fixtures/" . $file);
        }
        return $dataSet;
    }
}
