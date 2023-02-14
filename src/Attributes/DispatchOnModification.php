<?php

declare(strict_types=1);

namespace Medas\EntityEvents\Attributes;

#[\Attribute(\Attribute::TARGET_CLASS)]
class DispatchOnModification extends BaseDispatchEvent
{
}
