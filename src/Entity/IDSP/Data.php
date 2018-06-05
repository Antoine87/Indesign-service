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
 * IDSP entity Data
 *
 * @author     Antoine87 <antoine87@openmail.cc>
 */
class Data implements Entity
{
    /** @var mixed */
    private $data;


    /**
     * @param mixed $data
     * @throws \InvalidArgumentException
     */
    public function __construct($data)
    {
        $this->setData($data);
    }

    /**
     * Magic constructor
     *
     * @param array|\stdClass $object
     * @return Data
     * @throws \InvalidArgumentException
     */
    public static function new($object): Data
    {
        if (\is_array($object)) {
            return new self($object['data']);
        }
        if ($object instanceof \stdClass) {
            return new self($object->data);
        }
        throw new \InvalidArgumentException(self::class . ' magic constructor accepts only array or object');

    }


    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     * @return Data
     * @throws \InvalidArgumentException
     */
    public function setData($data): self
    {
        if (!is_scalar($data)
            && !\is_array($data)
            && !($data instanceof \stdClass)) {
            throw new \InvalidArgumentException('Invalid data passed');
        }
        $this->data = $data;
        return $this;
    }


    /**
     * {@inheritdoc}
     */
    public function serialize(): string
    {
        return serialize([
            'data' => $this->getData()
        ]);
    }

    /**
     * {@inheritdoc}
     * @throws \InvalidArgumentException
     */
    public function unserialize($serialized): void
    {
        $data = unserialize($serialized, ['allowed_classes' => false]);

        $this->setData($data['data']);
    }

}
