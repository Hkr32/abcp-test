<?php

namespace Nw\WebService\References\Staff;

/**
 * @property Seller $Seller
 */
class Seller extends Contractor
{
    const TYPE = 2;

    public function __construct(
        protected int $id,
        protected string $name = 'reseller',
        protected int $type = self::TYPE,
    ) {
        parent::__construct($id, $name, self::TYPE);
    }

    public static function getById(int $id): self
    {
        return new self($id); // fakes the getById method
    }
}