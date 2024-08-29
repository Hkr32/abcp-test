<?php

namespace Nw\WebService\References\Staff;

class Contractor
{
    private const TYPE = 1;

    public function __construct(
        protected int $id,
        protected string $name = 'contractor',
        protected int $type = self::TYPE,
    ) {
    }

    public function getFullName(): string
    {
        return $this->id.':'.$this->name.' ('.$this->type.')';
    }

    public static function getById(int $id): self
    {
        return new self($id); // fakes the getById method
    }

    public static function getCustomerTypeId(): int
    {
        return self::TYPE;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function getResellerEmailFrom(): string
    {
        return 'contractor@example.com';
    }

    public function getEmailsByPermit(int $resellerId, string $event): array
    {
        // fakes the method
        return ['someemeil@example.com', 'someemeil2@example.com'];
    }
}
