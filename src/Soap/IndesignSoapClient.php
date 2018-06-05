<?php

/*
 * This file is part of the Indesign-API package.
 *
 * (c) Antoine87 <antoine87@openmail.cc>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace IndesignService\Soap;

use IndesignService\Entity\IDSP\RunScript;
use IndesignService\Entity\IDSP\RunScriptParameters;
use IndesignService\Entity\IDSP\RunScriptResponse;
use SoapClient;

/**
 * Class IndesignSoapClient
 *
 * @author     Antoine87 <antoine87@openmail.cc>
 */
class IndesignSoapClient extends SoapClient
{
    protected const INDESIGN_SERVER_PROTOCOL = 'http';
    protected const INDESIGN_SERVER_WSDL = 'service?wsdl';

    /** @var string */
    protected $wsdl;

    /** @var string */
    private $host;

    /** @var int */
    private $port;


    /**
     * @param string $host
     * @param int    $port
     * @throws \InvalidArgumentException
     */
    public function __construct(string $host, int $port)
    {
        $this->setWsdl($host, $port);
        $this->host = $host;
        $this->port = $port;

        parent::__construct($this->wsdl, [
            'cache_wsdl' => WSDL_CACHE_NONE,
            'proxy_host' => $this->host,
            'proxy_port' => $this->port,
        ]);
    }


    /**
     * @return string
     */
    public function getWsdl(): string
    {
        return $this->wsdl;
    }

    /**
     * @param string $host
     * @param int    $port
     * @return IndesignSoapClient
     * @throws \InvalidArgumentException
     */
    public function setWsdl(string $host, int $port): self
    {
        $this->wsdl = static::getWsdlUri($host, $port);
        $this->host = $host;
        $this->port = $port;

        return $this;
    }


    /**
     * @param RunScriptParameters $runScriptParameters
     * @return RunScriptResponse
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function runScript(RunScriptParameters $runScriptParameters): RunScriptResponse
    {
        $response = parent::__soapCall('RunScript', [new RunScript($runScriptParameters)]);

        return $this->parseResponse($response);
    }

    /**
     * Returns an array of functions described in the WSDL for the Web service.
     *
     * @link http://php.net/manual/en/soapclient.getfunctions.php
     * @return array The array of SOAP function prototypes, detailing the return type,
     * the function name and type-hinted parameters.
     */
    public function getFunctions(): array
    {
        return parent::__getFunctions();
    }

    /**
     * Returns an array of types described in the WSDL for the Web service.
     *
     * @link http://php.net/manual/en/soapclient.gettypes.php
     * @return array The array of SOAP types, detailing all structures and types.
     */
    public function getTypes(): array
    {
        return parent::__getTypes();
    }

    /**
     * @param string $host
     * @param int    $port
     * @return string
     * @throws \InvalidArgumentException
     */
    public static function getWsdlUri(string $host, int $port): string
    {
        if (!static::isBetween($port, 1, 65535)) {
            throw new \InvalidArgumentException('The port must be between 1 and 65535');
        }

        $wsdl = self::INDESIGN_SERVER_PROTOCOL . "://{$host}:{$port}/" . self::INDESIGN_SERVER_WSDL;
        $wsdl = filter_var($wsdl, FILTER_SANITIZE_URL);

        if (!filter_var($wsdl, FILTER_VALIDATE_URL,
            FILTER_FLAG_SCHEME_REQUIRED | FILTER_FLAG_HOST_REQUIRED
            | FILTER_FLAG_PATH_REQUIRED | FILTER_FLAG_QUERY_REQUIRED)) {
            throw new \InvalidArgumentException('Invalid WSDL URI for SoapClient');
        }

        return $wsdl;
    }


    /**
     * @param mixed $response
     * @return RunScriptResponse
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    protected function parseResponse($response): RunScriptResponse
    {
        if (!$response instanceof \stdClass) {
            throw new \RuntimeException('Excepted object (stdClass) as response');
        }

        return RunScriptResponse::new($response);
    }


    private static function isBetween(int $val, int $min, int $max): bool
    {
        return $val >= $min && $val <= $max;
    }
}
