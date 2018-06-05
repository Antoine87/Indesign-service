<?php

/*
 * This file is part of the Indesign-API package.
 *
 * (c) Antoine87 <antoine87@openmail.cc>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace IndesignService;

use IndesignService\Entity\Exception\JSXException;
use IndesignService\Entity\IDSP\RunScriptParameters;
use IndesignService\Entity\IDSP\RunScriptResponse;
use IndesignService\Entity\IDSP\ScriptArg;
use IndesignService\Entity\Script\ExtendedResponse;
use IndesignService\Soap\IndesignSoapClient;

/**
 * Class IndesignService
 *
 * @author     Antoine87 <antoine87@openmail.cc>
 */
class IndesignService
{
    /** @var IndesignSoapClient */
    private $soapClient;

    /** @var string */
    private $serviceScriptPath;

    /** @var string */
    private $publicScriptPath;

    /** @var string */
    private $indesignPrefix;

    /** @var string */
    private $localPrefix;


    /**
     * @param string      $host
     * @param int         $port
     * @param string|null $indesignPrefix
     * @param string|null $localPrefix
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function __construct(string $host, int $port, string $indesignPrefix = null, string $localPrefix = null)
    {
        $this->bootstrap($indesignPrefix, $localPrefix);

        $this->soapClient = new IndesignSoapClient($host, $port);
    }


    /**
     * @return bool
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function checkConnection(): bool
    {
        $inputValue = (string)time();
        $outputValue = $this->runServiceScriptFile('connection-test.jsx', [new ScriptArg('test', $inputValue)]);

        return $outputValue === $inputValue;
    }

    /**
     * @param RunScriptParameters $runScriptParameters
     * @return mixed
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function runScript(RunScriptParameters $runScriptParameters)
    {
        $response = $this->callRunScript($runScriptParameters);

        return $response->getScriptResult()->getData();
    }

    /**
     * @param string      $scriptFile
     * @param ScriptArg[] $scriptArgs
     * @param string      $scriptLanguage
     * @return mixed
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function runScriptFile(string $scriptFile, array $scriptArgs,
                                  string $scriptLanguage = RunScriptParameters::LANGUAGE_JAVASCRIPT)
    {
        $script = $this->getFile($scriptFile);
        $runScriptParameters = new RunScriptParameters($script, $scriptArgs, $scriptLanguage);

        return $this->runScript($runScriptParameters);
    }

    /**
     * @param RunScriptParameters $runScriptParameters
     * @return mixed
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @throws JSXException
     */
    public function runScriptExtended(RunScriptParameters $runScriptParameters)
    {
        $this->prepareExtendedScript($runScriptParameters);
        $response = $this->callRunScript($runScriptParameters);

        return $this->parseExtendedResponse($response);
    }

    /**
     * @param string      $scriptFile
     * @param ScriptArg[] $scriptArgs
     * @param string      $scriptLanguage
     * @return mixed
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function runPublicScriptFile(string $scriptFile, array $scriptArgs,
                                        string $scriptLanguage = RunScriptParameters::LANGUAGE_JAVASCRIPT)
    {
        $script = $this->getFile($this->publicScriptPath . "/{$scriptFile}");
        $runScriptParameters = new RunScriptParameters($script, $scriptArgs, $scriptLanguage);

        return $this->runScript($runScriptParameters);
    }

    /**
     * @param string      $scriptFile
     * @param ScriptArg[] $scriptArgs
     * @param string      $scriptLanguage
     * @return mixed
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @throws JSXException
     */
    public function runScriptFileExtended(string $scriptFile, array $scriptArgs,
                                          string $scriptLanguage = RunScriptParameters::LANGUAGE_JAVASCRIPT)
    {
        $script = $this->getFile($this->publicScriptPath . "/{$scriptFile}");
        $runScriptParameters = new RunScriptParameters($script, $scriptArgs, $scriptLanguage);

        return $this->runScriptExtended($runScriptParameters);
    }


    /**
     * @param string $path
     * @return string
     */
    protected function prefixMountPath(string $path): string
    {
        $mountPoint = rtrim(trim($this->localPrefix), '\\/') . '/';
        $remoteMountPoint = rtrim(trim($this->indesignPrefix), '\\/') . '/';

        $pos = strpos($path, $mountPoint);

        if ($pos !== false) {
            return substr_replace($path, $remoteMountPoint, $pos, \strlen($mountPoint));
        }
        return $path;
    }

    /**
     * @param RunScriptParameters $runScriptParameters
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    protected function prepareExtendedScript(RunScriptParameters $runScriptParameters): void
    {
        $passedScript = $runScriptParameters->getScriptText();

        $runScriptParameters->setScriptArgsLast(new ScriptArg('__script__', $passedScript));
        $runScriptParameters->setScriptText($this->getExtendedWrapperScript());
    }

    /**
     * @param RunScriptResponse $response
     * @return mixed
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @throws JSXException
     */
    protected function parseExtendedResponse(RunScriptResponse $response)
    {
        $result = $response->getScriptResult()->getData();

        if ($response->getErrorNumber() === 0) {
            $extendedResponse = new ExtendedResponse($result);

            if (!$extendedResponse->isSuccess()) {
                throw new JSXException($extendedResponse->getException());
            }
            $result = $extendedResponse->getResult();
        }
        return $result;
    }

    /**
     * @return string
     * @throws \RuntimeException
     */
    protected function getExtendedWrapperScript(): string
    {
        $jsonLib = $this->getServiceScriptFile('json2.js');
        $extendedWrapper = $this->getServiceScriptFile('extended-wrapper.jsx');
        $extendedFunctions = $this->getServiceScriptFile('extended-functions.jsx');

        return str_replace(
            ['/*{{ json_lib }}*/', '/*{{ public_functions }}*/'],
            [$jsonLib, $extendedFunctions],
            $extendedWrapper
        );
    }

    /**
     * @param string      $scriptFile
     * @param ScriptArg[] $scriptArgs
     * @return mixed
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    protected function runServiceScriptFile(string $scriptFile, array $scriptArgs)
    {
        $scriptText = $this->getServiceScriptFile($scriptFile);

        $params = new RunScriptParameters($scriptText, $scriptArgs, RunScriptParameters::LANGUAGE_JAVASCRIPT);

        return $this->runScript($params);
    }

    /**
     * @param RunScriptParameters $runScriptParameters
     * @return RunScriptResponse
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    protected function callRunScript(RunScriptParameters $runScriptParameters): RunScriptResponse
    {
        foreach ($runScriptParameters->getScriptArgs() as $scriptArg) {

            if ($scriptArg->getType() === ScriptArg::TYPE_PATH) {
                $scriptArg->setValue($this->prefixMountPath($scriptArg->getValue()));
            }
        }
        return $this->soapClient->runScript($runScriptParameters);
    }

    /**
     * @param string $scriptFile
     * @return string
     * @throws \RuntimeException
     */
    protected function getServiceScriptFile(string $scriptFile): string
    {
        return $this->getFile($this->serviceScriptPath . "/{$scriptFile}");
    }

    /**
     * @param string $scriptFile
     * @return string
     * @throws \RuntimeException
     */
    protected function getPublicScriptFile(string $scriptFile): string
    {
        return $this->getFile($this->publicScriptPath . "/{$scriptFile}");
    }

    /**
     * @param string $file
     * @return string
     * @throws \RuntimeException
     */
    protected function getFile($file): string
    {
        if (!is_readable($file)) {
            throw new \RuntimeException("Cannot read file '{$file}'");
        }
        $text = file_get_contents($file);

        if ($text === false) {
            throw new \RuntimeException("Cannot read file '{$file}'");
        }
        return $text;
    }


    /**
     * Setup properties of this service
     *
     * @param string|null $indesignPrefix
     * @param string|null $localPrefix
     */
    private function bootstrap(string $indesignPrefix = null, string $localPrefix = null): void
    {
        $rootPath = realpath(__DIR__ . '/../');

        $this->serviceScriptPath = "{$rootPath}/src/Scripts";
        $this->publicScriptPath = "{$rootPath}/scripts";

        $this->indesignPrefix = $indesignPrefix;
        $this->localPrefix = $localPrefix;
    }
}
