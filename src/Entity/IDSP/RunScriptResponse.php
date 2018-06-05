<?php

/*
 * This file is part of the Indesign-API package.
 *
 * (c) Antoine87 <antoine87@openmail.cc>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace IndesignService\Entity\IDSP;

/**
 * IDSP entity RunScriptResponse
 *
 * @author     Antoine87 <antoine87@openmail.cc>
 */
class RunScriptResponse implements Entity
{
    /** @var int */
    private $errorNumber;

    /** @var null|string */
    private $errorString;

    /** @var Data */
    private $scriptResult;


    /**
     * @param int         $errorNumber
     * @param null|string $errorString
     * @param Data        $scriptResult
     */
    public function __construct(int $errorNumber, ?string $errorString, Data $scriptResult)
    {
        $this->setErrorNumber($errorNumber);
        $this->setErrorString($errorString);
        $this->setScriptResult($scriptResult);
    }

    /**
     * Magic constructor
     *
     * @param array|\stdClass $object
     * @return RunScriptResponse
     * @throws \InvalidArgumentException
     */
    public static function new($object): RunScriptResponse
    {
        if (\is_array($object)) {
            return new self(
                $object['errorNumber'],
                $object['errorString'] ?? null,
                Data::new($object['scriptResult'])
            );
        }
        if ($object instanceof \stdClass) {
            return new self(
                $object->errorNumber,
                $object->errorString ?? null,
                Data::new($object->scriptResult)
            );
        }
        throw new \InvalidArgumentException(self::class . ' magic constructor accepts only array or object');

    }


    /**
     * @return int
     */
    public function getErrorNumber(): int
    {
        return $this->errorNumber;
    }

    /**
     * @return string
     */
    public function getErrorString(): string
    {
        return $this->errorString;
    }

    /**
     * @return Data
     */
    public function getScriptResult(): Data
    {
        return $this->scriptResult;
    }

    /**
     * @param int $errorNumber
     * @return RunScriptResponse
     */
    public function setErrorNumber(int $errorNumber): RunScriptResponse
    {
        $this->errorNumber = $errorNumber;
        return $this;
    }

    /**
     * @param null|string $errorString
     * @return RunScriptResponse
     */
    public function setErrorString(?string $errorString): RunScriptResponse
    {
        $this->errorString = $errorString;
        return $this;
    }

    /**
     * @param Data $scriptResult
     * @return RunScriptResponse
     */
    public function setScriptResult(Data $scriptResult): RunScriptResponse
    {
        $this->scriptResult = $scriptResult;
        return $this;
    }


    /**
     * {@inheritdoc}
     */
    public function serialize(): string
    {
        return serialize([
            'errorNumber' => $this->getErrorNumber(),
            'errorString' => $this->getErrorString(),
            'scriptResult' => $this->getScriptResult()
        ]);
    }

    /**
     * {@inheritdoc}
     * @throws \InvalidArgumentException
     */
    public function unserialize($serialized): void
    {
        $data = unserialize($serialized, ['allowed_classes' => false]);

        $this->setErrorNumber($data['errorNumber']);
        $this->setErrorString($data['errorString']);
        $this->setScriptResult($data['scriptResult']);
    }
}
