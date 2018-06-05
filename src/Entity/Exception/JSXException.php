<?php

/*
 * This file is part of the Indesign-API package.
 *
 * (c) Antoine87 <antoine87@openmail.cc>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace IndesignService\Entity\Exception;

/**
 * Class JSXException
 *
 * @author     Antoine87 <antoine87@openmail.cc>
 */
class JSXException extends \Exception
{
    /** @var \stdClass */
    protected $extended;

    /**
     * @param \stdClass $scriptResult
     * @throws \InvalidArgumentException
     */
    public function __construct(\stdClass $scriptResult)
    {
        $this->extended = $scriptResult;

        parent::__construct(
            "[Error {$this->scriptErrorNumber()}] {$this->scriptErrorName()}: {$this->scriptErrorMessage()}"
        );
    }

    /**
     * @return int
     */
    public function scriptErrorNumber(): int
    {
        return $this->extended->number;
    }

    /**
     * @return string
     */
    public function scriptErrorFileName(): string
    {
        return $this->extended->fileName;
    }

    /**
     * @return int
     */
    public function scriptErrorLine(): int
    {
        return $this->extended->line;
    }

    /**
     * @return string
     */
    public function scriptErrorSource(): string
    {
        return $this->extended->source;
    }

    /**
     * @return int
     */
    public function scriptErrorStart(): int
    {
        return $this->extended->start;
    }

    /**
     * @return int
     */
    public function scriptErrorEnd(): int
    {
        return $this->extended->end;
    }

    /**
     * @return string
     */
    public function scriptErrorMessage(): string
    {
        return $this->extended->message;
    }

    /**
     * @return string
     */
    public function scriptErrorName(): string
    {
        return $this->extended->name;
    }
}
