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
 * IDSP entity RunScript
 *
 * @author     Antoine87 <antoine87@openmail.cc>
 */
class RunScript implements Entity
{
    /** @var RunScriptParameters */
    private $runScriptParameters;


    /**
     * @param RunScriptParameters $runScriptParameters
     */
    public function __construct(RunScriptParameters $runScriptParameters)
    {
        $this->setRunScriptParameters($runScriptParameters);
    }

    /**
     * Magic constructor
     *
     * @param array|\stdClass $object
     * @return RunScript
     * @throws \InvalidArgumentException
     */
    public static function new($object): RunScript
    {
        if (\is_array($object)) {
            return new self($object['runScriptParameters']);
        }
        if ($object instanceof \stdClass) {
            return new self($object->runScriptParameters);
        }
        throw new \InvalidArgumentException(self::class . ' magic constructor accepts only array or object');

    }


    /**
     * @return RunScriptParameters
     */
    public function getRunScriptParameters(): RunScriptParameters
    {
        return $this->runScriptParameters;
    }

    /**
     * @param RunScriptParameters $runScriptParameters
     * @return RunScript
     */
    public function setRunScriptParameters(RunScriptParameters $runScriptParameters): self
    {
        $this->runScriptParameters = $runScriptParameters;
        return $this;
    }


    /**
     * {@inheritdoc}
     */
    public function serialize(): string
    {
        return serialize([
            'runScriptParameters' => $this->getRunScriptParameters()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized): void
    {
        $data = unserialize($serialized, ['allowed_classes' => false]);

        $this->setRunScriptParameters($data['runScriptParameters']);
    }

}
