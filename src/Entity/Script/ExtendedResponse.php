<?php

/*
 * This file is part of the Indesign-API package.
 *
 * (c) Antoine87 <antoine87@openmail.cc>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace IndesignService\Entity\Script;

/**
 * Entity ExtendedResponse
 *
 * @author     Antoine87 <antoine87@openmail.cc>
 */
class ExtendedResponse implements \Serializable, \JsonSerializable
{
    /** @var bool */
    private $success;

    /** @var mixed */
    private $result;

    /** @var \stdClass */
    private $exception;


    /**
     * @param string $resultData
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function __construct(string $resultData)
    {
        $result = json_decode($resultData);

        if ($result === null || !\is_object($result) || !isset($result->success)
            || (!isset($result->result) && !isset($result->exception))
        ) {
            throw new \RuntimeException('Cannot decode or bad JSON response');
        }
        $this->setSuccess($result->success);

        if ($this->isSuccess()) {
            $this->setResult($result->result);
        } else {
            if (!isset(
                $result->exception->number,
                $result->exception->fileName,
                $result->exception->line,
                $result->exception->source,
                $result->exception->start,
                $result->exception->end,
                $result->exception->message,
                $result->exception->name
            )) {
                throw new \InvalidArgumentException('Bad exception format.');
            }
            $this->setException($result->exception);
        }
    }


    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @return \stdClass
     */
    public function getException(): \stdClass
    {
        return $this->exception;
    }

    /**
     * @param string $success
     * @return ExtendedResponse
     */
    public function setSuccess(string $success): ExtendedResponse
    {
        $this->success = $success;
        return $this;
    }

    /**
     * @param mixed $result
     * @return ExtendedResponse
     */
    public function setResult($result): ExtendedResponse
    {
        $this->result = $result;
        return $this;
    }

    /**
     * @param \stdClass $exception
     * @return ExtendedResponse
     */
    public function setException(\stdClass $exception): ExtendedResponse
    {
        $this->exception = $exception;
        return $this;
    }


    /**
     * {@inheritdoc}
     */
    public function serialize(): string
    {
        return serialize([
            'success' => $this->isSuccess(),
            'result' => $this->getResult(),
            'exception' => $this->getException()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized): void
    {
        $data = unserialize($serialized, ['allowed_classes' => false]);

        $this->setSuccess($data['success']);
        $this->setResult($data['result']);
        $this->setException($data['exception']);
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'success' => $this->isSuccess(),
            'exception' => $this->getException(),
            'result' => $this->getResult()
        ];
    }
}
