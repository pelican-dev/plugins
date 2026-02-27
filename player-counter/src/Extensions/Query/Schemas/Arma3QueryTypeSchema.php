<?php

namespace Boy132\PlayerCounter\Extensions\Query\Schemas;

class Arma3QueryTypeSchema extends SourceQueryTypeSchema
{
    public function getId(): string
    {
        return 'arma3';
    }

    public function getName(): string
    {
        return 'Arma 3';
    }
}
