<?php

declare(strict_types=1);

namespace Cadexsa\Infrastructure\Persistence\Contracts;

interface ColumnMap
{
    /**
     * Gets the column name(s).
     * 
     * @return string[]|string
     */
    public function getColumnName();

    /**
     * Gets the field name.
     * 
     * @return string
     */
    public function getFieldName();

    /**
     * Gets the value of the field in a given object.
     *
     * @param object $subject
     * @return mixed
     */
    public function getFieldValue(object $subject);
}
