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
 * Class IndesignService
 *
 * @author     Antoine87 <antoine87@openmail.cc>
 */
interface Entity extends \Serializable
{
    /**
     * Magic constructor
     *
     * @param mixed $object
     * @return self|void
     */
    public static function new($object);
}
