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
 * IDSP entity ScriptArg
 *
 * @author     Antoine87 <antoine87@openmail.cc>
 */
class ScriptArg implements Entity
{
    public const TYPE_STRING = 0;
    public const TYPE_PATH = 1;

    /** @var string */
    private $name;

    /** @var null|string */
    private $value;

    /** @var int */
    private $type;


    /**
     * @param string      $name
     * @param null|string $value
     * @param int         $type
     * @throws \InvalidArgumentException
     */
    public function __construct(string $name, ?string $value = null, int $type = self::TYPE_STRING)
    {
        $this->setName($name);
        $this->setValue($value);
        $this->setType($type);
    }

    /**
     * Magic constructor
     *
     * @param array|\stdClass $object
     * @return ScriptArg
     * @throws \InvalidArgumentException
     */
    public static function new($object): ScriptArg
    {
        if (\is_array($object)) {
            return new self(
                $object['name'],
                $object['value'] ?? null,
                $object['type'] ?? null
            );
        }
        if ($object instanceof \stdClass) {
            return new self(
                $object->name,
                $object->value ?? null
            );
        }
        throw new \InvalidArgumentException(self::class . ' magic constructor accepts only array or object');

    }


    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return null|string
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @param string $name
     * @return ScriptArg
     */
    public function setName(string $name): ScriptArg
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param null|string $value
     * @return ScriptArg
     */
    public function setValue(?string $value): ScriptArg
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @param int $type
     * @return ScriptArg
     * @throws \InvalidArgumentException
     */
    public function setType(int $type): ScriptArg
    {
        if (!\in_array($type, [
            self::TYPE_STRING,
            self::TYPE_PATH
        ], true)) {
            throw new \InvalidArgumentException('Invalid script argument type');
        }
        $this->type = $type;
        return $this;
    }


    /**
     * {@inheritdoc}
     */
    public function serialize(): string
    {
        return serialize([
            'name' => $this->getName(),
            'value' => $this->getValue(),
            'type' => $this->getType()
        ]);
    }

    /**
     * {@inheritdoc}
     * @throws \InvalidArgumentException
     */
    public function unserialize($serialized): void
    {
        $data = unserialize($serialized, ['allowed_classes' => false]);

        $this->setName($data['name']);
        $this->setValue($data['value']);
        $this->setType($data['type']);
    }

}
