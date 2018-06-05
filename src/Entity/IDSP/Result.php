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
 * IDSP entity Result
 *
 * @author     Antoine87 <antoine87@openmail.cc>
 */
class Result implements Entity
{
    /** @var int */
    private $errorCode;


    /**
     * @param int $errorCode
     */
    public function __construct(int $errorCode)
    {
        $this->setErrorCode($errorCode);
    }

    /**
     * Magic constructor
     *
     * @param array|\stdClass $object
     * @return Result
     * @throws \InvalidArgumentException
     */
    public static function new($object): Result
    {
        if (\is_array($object)) {
            return new self($object['errorCode']);
        }
        if ($object instanceof \stdClass) {
            return new self($object->errorCode);
        }
        throw new \InvalidArgumentException(self::class . ' magic constructor accepts only array or object');

    }


    /**
     * @return int
     */
    public function getErrorCode(): int
    {
        return $this->errorCode;
    }

    /**
     * @param int $errorCode
     * @return Result
     */
    public function setErrorCode(int $errorCode): self
    {
        $this->errorCode = $errorCode;
        return $this;
    }


    /**
     * {@inheritdoc}
     */
    public function serialize(): string
    {
        return serialize([
            'errorCode' => $this->getErrorCode()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized): void
    {
        $data = unserialize($serialized, ['allowed_classes' => false]);

        $this->setErrorCode($data['errorCode']);
    }

}
