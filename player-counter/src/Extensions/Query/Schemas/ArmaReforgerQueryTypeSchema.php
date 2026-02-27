<?php

namespace Boy132\PlayerCounter\Extensions\Query\Schemas;

class ArmaReforgerQueryTypeSchema extends SourceQueryTypeSchema
{
    public function getId(): string
    {
        return 'arma-reforger';
    }

    public function getName(): string
    {
        return 'Arma Reforger';
    }
}
