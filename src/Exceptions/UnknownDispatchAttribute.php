<?php

declare(strict_types=1);

namespace Medas\EntityEvents\Exceptions;

use Medas\Core\Exceptions\BaseException;

class UnknownDispatchAttribute extends BaseException
{
    public function __construct(string $attribute)
    {
        parent::__construct($attribute);
    }

    public function pattern(): string
    {
        return 'Unknown dispatch attribute: %s';
    }
}
