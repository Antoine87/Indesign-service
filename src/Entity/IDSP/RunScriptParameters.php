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
 * IDSP entity RunScriptParameters
 *
 * @author     Antoine87 <antoine87@openmail.cc>
 */
class RunScriptParameters implements Entity
{
    public const LANGUAGE_JAVASCRIPT = 'javascript';
    public const LANGUAGE_APPLESCRIPT = 'applescript';
    public const LANGUAGE_VISUAL_BASIC = 'visual basic';

    /** @var null|string */
    private $scriptText;

    /** @var ScriptArg[] */
    private $scriptArgs;

    /** @var string */
    private $scriptLanguage;

    /** @var null|string */
    private $scriptFile;


    /**
     * @param null|string $scriptText
     * @param ScriptArg[] $scriptArgs
     * @param string      $scriptLanguage
     * @param null|string $scriptFile
     * @throws \InvalidArgumentException
     */
    public function __construct(?string $scriptText, array $scriptArgs = [],
                                string $scriptLanguage = self::LANGUAGE_JAVASCRIPT, ?string $scriptFile = null)
    {
        $this->setScriptText($scriptText);
        $this->setScriptArgs($scriptArgs);
        $this->setScriptLanguage($scriptLanguage);
        $this->setScriptFile($scriptFile);
    }

    /**
     * Magic constructor
     *
     * @param array|\stdClass $object
     * @return RunScriptParameters
     * @throws \InvalidArgumentException
     */
    public static function new($object): RunScriptParameters
    {
        if (\is_array($object)) {
            return new self(
                $object['scriptText'],
                $object['scriptArgs'],
                $object['scriptLanguage'],
                $object['scriptFile']
            );
        }
        if ($object instanceof \stdClass) {
            return new self(
                $object->scriptText,
                $object->scriptArgs,
                $object->scriptLanguage,
                $object->scriptFile
            );
        }
        throw new \InvalidArgumentException(self::class . ' magic constructor accepts only array or object');

    }


    /**
     * @return null|string
     */
    public function getScriptText(): ?string
    {
        return $this->scriptText;
    }

    /**
     * @return ScriptArg[]
     */
    public function getScriptArgs(): array
    {
        return $this->scriptArgs;
    }

    /**
     * @return string
     */
    public function getScriptLanguage(): string
    {
        return $this->scriptLanguage;
    }

    /**
     * @return null|string
     */
    public function getScriptFile(): ?string
    {
        return $this->scriptFile;
    }

    /**
     * @param null|string $scriptText
     * @return RunScriptParameters
     */
    public function setScriptText(?string $scriptText): self
    {
        $this->scriptText = $scriptText;
        return $this;
    }

    /**
     * @param ScriptArg[] $scriptArgs
     * @return RunScriptParameters
     * @throws \InvalidArgumentException
     */
    public function setScriptArgs(array $scriptArgs): self
    {
        foreach ($scriptArgs as $scriptArg) {
            if (!$scriptArg instanceof ScriptArg) {
                throw new \InvalidArgumentException('Every scriptArgs elements must be an instance of ScriptArg');
            }
        }
        $this->scriptArgs = $scriptArgs;
        return $this;
    }

    /**
     * @param ScriptArg $scriptArg
     * @return RunScriptParameters
     * @throws \InvalidArgumentException
     */
    public function setScriptArgsFirst(ScriptArg $scriptArg): self
    {
        array_unshift($this->scriptArgs, $scriptArg);
        return $this;
    }


    /**
     * @param ScriptArg $scriptArg
     * @return RunScriptParameters
     * @throws \InvalidArgumentException
     */
    public function setScriptArgsLast(ScriptArg $scriptArg): self
    {
        $this->scriptArgs[] = $scriptArg;
        return $this;
    }

    /**
     * @param string $scriptLanguage
     * @return RunScriptParameters
     * @throws \InvalidArgumentException
     */
    public function setScriptLanguage(string $scriptLanguage): self
    {
        if (!\in_array($scriptLanguage, [
            self::LANGUAGE_JAVASCRIPT,
            self::LANGUAGE_APPLESCRIPT,
            self::LANGUAGE_VISUAL_BASIC
        ], true)) {
            throw new \InvalidArgumentException('Invalid Indesign scripting language');
        }
        $this->scriptLanguage = $scriptLanguage;
        return $this;
    }

    /**
     * @param null|string $scriptFile
     * @return RunScriptParameters
     */
    public function setScriptFile(?string $scriptFile): RunScriptParameters
    {
        $this->scriptFile = $scriptFile;
        return $this;
    }


    /**
     * {@inheritdoc}
     */
    public function serialize(): string
    {
        return serialize([
            'scriptText' => $this->getScriptText(),
            'scriptArgs' => $this->getScriptArgs(),
            'scriptLanguage' => $this->getScriptLanguage(),
            'scriptFile' => $this->getScriptFile()
        ]);
    }

    /**
     * {@inheritdoc}
     * @throws \InvalidArgumentException
     */
    public function unserialize($serialized): void
    {
        $data = unserialize($serialized, ['allowed_classes' => false]);

        $this->setScriptText($data['scriptText']);
        $this->setScriptArgs($data['scriptArgs']);
        $this->setScriptLanguage($data['scriptLanguage']);
        $this->setScriptFile($data['scriptFile']);
    }

}
