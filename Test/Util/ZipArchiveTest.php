<?php

namespace Velikonja\LabbyBundle\Test\Util;

use Symfony\Component\Filesystem\Filesystem;
use Velikonja\LabbyBundle\Util\ZipArchive;

/**
 * Class ZipArchiveTest
 *
 * @package Velikonja\LabbyBundle\Test\Util
 */
class ZipArchiveTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    private $tmpDir;

    /**
     * Create temporary directory, if exists removes it first.
     */
    public function setUp()
    {
        $this->tmpDir = sys_get_temp_dir() . '/tmp'; //TODO: change path

        $fs = new Filesystem();
        $fs->remove($this->tmpDir);
        $fs->mkdir($this->tmpDir);
    }

    /**
     * Remove temporary directory.
     */
    public function tearDown()
    {
        $fs = new Filesystem();
        $fs->remove($this->tmpDir);
    }

    /**
     * Tests if current file content is zipped.
     *
     * @throws \Exception
     */
    public function testSimpleZipOfContent()
    {
        $tmpFile = $this->tmpDir . '/archive.zip';

        $zip = new ZipArchive($this->tmpDir);
        $zip->zip($tmpFile, file_get_contents(__FILE__));

        $this->assertTrue(
            file_exists($tmpFile),
            "File `$tmpFile` not created."
        );
    }

    /**
     * Tests if unzipping works.
     */
    public function testSimpleUnzipOfContent()
    {
        $tmpZipFile = $this->tmpDir . '/test.zip';

        $zip = new \ZipArchive($this->tmpDir);
        $zip->open($tmpZipFile, \ZipArchive::CREATE);
        $zip->addFromString('dump.sql', '');
        $zip->close();

        $zip          = new ZipArchive($this->tmpDir);
        $unzippedFile = $zip->unzip($tmpZipFile);

        $this->assertFileExists($unzippedFile, 'Unzipped file was not found.');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testIfExceptionIsThrownWhenCompressedFileIsNotFound()
    {
        $tmpZipFile = $this->tmpDir . '/test.zip';

        $zip = new \ZipArchive();
        $zip->open($tmpZipFile, \ZipArchive::CREATE);
        $zip->addFromString('foo.bar', '');
        $zip->close();

        $zip = new ZipArchive($this->tmpDir);
        $zip->unzip($tmpZipFile);
    }

    /**
     * ZipArchive should throw exception if directory is not writable.
     *
     * @expectedException \Exception
     */
    public function testThrowExceptionIfTempDirIsNotWritable()
    {
        $unwritableDir = $this->tmpDir . '/unwritable';

        mkdir($unwritableDir, 0444); // write only dir

        new ZipArchive($unwritableDir);
    }

    /**
     * Should return false if file is not zip.
     */
    public function testShouldReturnFalseIfFileIsNotZip()
    {
        $zip = new ZipArchive();

        $this->assertFalse(
            $zip->isZipped(__FILE__),
            basename(__FILE__) . ' is not zipped file, but ZipArchive reports it is.'
        );
    }
}
