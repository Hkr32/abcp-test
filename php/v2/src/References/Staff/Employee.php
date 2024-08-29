<?php

namespace Nw\WebService\References\Staff;

/**
 * @property Seller $Seller
 */
class Employee extends Contractor
{
    const TYPE = 3;

    public function __construct(
        protected int $id,
        protected string $name = 'employee',
        protected int $type = self::TYPE,
    ) {
        parent::__construct($id, $name, self::TYPE);
    }

    public static function getById(int $id): self
    {
        return new self($id); // fakes the getById method
    }
}