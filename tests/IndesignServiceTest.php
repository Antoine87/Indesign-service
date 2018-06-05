<?php

/*
 * This file is part of the Indesign-API package.
 *
 * (c) Antoine87 <antoine87@openmail.cc>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace IndesignService\Tests;

use IndesignService\Entity\IDSP\RunScriptParameters;
use IndesignService\Entity\IDSP\ScriptArg;
use IndesignService\IndesignService;
use PHPUnit\Framework\TestCase;

/**
 * @covers IndesignService
 */
class IndesignServiceTest extends TestCase
{
    public function configProvider(): array
    {
        $config = include __DIR__ . '/config.php';
        return [[$config]];
    }

    /**
     * @dataProvider configProvider
     * @runInSeparateProcess
     *
     * @param array $config
     */
    public function testConnection(array $config): void
    {
        $is = new IndesignService($config['host'], $config['port']);

        $this->assertTrue($is->checkConnection());
    }

    /**
     * @dataProvider configProvider
     * @runInSeparateProcess
     *
     * @param array $config
     */
    public function testRunScript(array $config): void
    {
        $is = new IndesignService($config['host'], $config['port']);

        $testArg = time();
        $scriptText = 'var arg = app.scriptArgs.getValue("test"); arg;';
        $scripArg = new ScriptArg('test', $testArg);
        $params = new RunScriptParameters($scriptText, [$scripArg], RunScriptParameters::LANGUAGE_JAVASCRIPT);

        $this->assertEquals($testArg, $is->runScript($params));
    }

    /**
     * @dataProvider configProvider
     * @runInSeparateProcess
     *
     * @param array $config
     * @throws \IndesignService\Entity\Exception\JSXException
     */
    public function testRunScriptExtended(array $config): void
    {
        $is = new IndesignService($config['host'], $config['port']);

        $testArg = time();
        $scriptText = 'var arg = app.scriptArgs.getValue("test"); var ret = {arg: arg}; ret;';
        $scripArg = new ScriptArg('test', $testArg);
        $params = new RunScriptParameters($scriptText, [$scripArg], RunScriptParameters::LANGUAGE_JAVASCRIPT);

        $result = $is->runScriptExtended($params);

        $this->assertInternalType('object', $result);
        $this->assertObjectHasAttribute('arg', $result);
        $this->assertEquals($testArg, $result->arg);
    }

    /**
     * @dataProvider configProvider
     * @runInSeparateProcess
     *
     * @param array $config
     */
    public function testRunScriptFile(array $config): void
    {
        $is = new IndesignService($config['host'], $config['port']);

        $testArg = time();
        $scriptArg = new ScriptArg('test', $testArg);

        $result = $is->runScriptFile(__DIR__ . '/Scripts/simple-test.jsx', [$scriptArg]);

        $this->assertEquals($testArg, $result);
    }

    /**
     * @dataProvider configProvider
     * @runInSeparateProcess
     *
     * @param array $config
     * @throws \IndesignService\Entity\Exception\JSXException
     */
    public function testRunScriptFileExtended(array $config): void
    {
        $is = new IndesignService($config['host'], $config['port']);

        $testArg = time();
        $scriptArg = new ScriptArg('test', $testArg);

        $result = $is->runScriptFileExtended(__DIR__ . '/Scripts/extended-test.jsx', [$scriptArg]);

        $this->assertEquals($testArg, $result);
    }

    /**
     * @dataProvider configProvider
     * @runInSeparateProcess
     *
     * @param array $config
     * @throws \IndesignService\Entity\Exception\JSXException
     */
    public function testExportXml(array $config): void
    {
        $is = new IndesignService($config['host'], $config['port']);

        $indesignFile = new ScriptArg('indesignFile', $config['exportXmlFileInput']);
        $xmlFile = new ScriptArg('xmlFile', $config['exportXmlFileOutput']);

        $response = $is->runScriptFileExtended('export-xml.jsx', [$indesignFile, $xmlFile]);

        $this->assertEquals(1, $response->nbPages);
        $this->assertFileExists($config['exportXmlFileOutputReadFrom']);
    }

    /**
     * @dataProvider configProvider
     * @runInSeparateProcess
     *
     * @param array $config
     * @throws \IndesignService\Entity\Exception\JSXException
     */
    public function testExportXmlMountedFolder(array $config): void
    {
        $is = new IndesignService($config['host'], $config['port'], $config['indesignPrefix'], $config['localPrefix']);

        $indesignFile = new ScriptArg('indesignFile', $config['exportXmlFileInputMF'], ScriptArg::TYPE_PATH);
        $xmlFile = new ScriptArg('xmlFile', $config['exportXmlFileOutputMF'], ScriptArg::TYPE_PATH);

        $response = $is->runScriptFileExtended('export-xml.jsx', [$indesignFile, $xmlFile]);

        $this->assertEquals(1, $response->nbPages);
    }
}
