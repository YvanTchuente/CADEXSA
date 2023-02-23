<?php

declare(strict_types=1);

namespace Cadexsa\Infrastructure\Persistence;

/**
 * Object-Relational mapping metadata parser.
 */
class MetadataParser
{
    private \XMLParser $parser;

    private string $metadata;

    private DataMap $dataMap;

    private array $currentEmbeddedField = [];

    private array $currentRelationField = [];

    public function __construct(string $metadataFilename)
    {
        if (!file_exists($metadataFilename)) {
            throw new \RuntimeException("$metadataFilename does not exist");
        }
        $this->parser = xml_parser_create();
        $this->metadata = file_get_contents($metadataFilename);
        xml_set_object($this->parser, $this);
        xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, false);
        xml_parser_set_option($this->parser, XML_OPTION_SKIP_WHITE, true);
        xml_set_element_handler($this->parser, "startElementHandler", "endElementHandler");
    }

    public function parse(): DataMap
    {
        xml_parse($this->parser, $this->metadata);
        xml_parser_free($this->parser);
        return $this->dataMap;
    }

    private function startElementHandler($parser, $element, $attributes)
    {
        switch ($element) {
            case 'entity':
                extract($attributes);
                $this->dataMap = new DataMap($table, $name);
                break;
            case 'field':
                extract($attributes);
                $columnMap = new ColumnMap($column, $name, $this->dataMap);
                $this->dataMap->addColumn($columnMap);
                break;
            case 'embedded-field':
                extract($attributes);
                $this->currentEmbeddedField['name'] = $name;
                break;
            case 'attribute':
                extract($attributes);
                $attribute = array('name' => $name, 'column' => $column);
                $this->currentEmbeddedField['attributes'][] = $attribute;
                break;
            case 'relation-field':
                extract($attributes);
                $this->currentRelationField['name'] = $name;
                break;
            case 'foreign-key-column':
                $name = $attributes['name'];
                $referencedField = $attributes['referenced-field-name'];
                $this->currentRelationField['column']['name'] = $name;
                $this->currentRelationField['column']['correspondent'] = $referencedField;
                break;
        }
    }

    private function endElementHandler($parser, $element)
    {
        switch ($element) {
            case 'embedded-field':
                $fieldName = $this->currentEmbeddedField['name'];
                $columnMap = new EmbeddedFieldColumnMap($this->dataMap, $fieldName, ...$this->currentEmbeddedField['attributes']);
                $this->dataMap->addColumn($columnMap);
                $this->currentEmbeddedField = [];
                break;
            case 'relation-field':
                $fieldName = $this->currentRelationField['name'];
                $columnName = $this->currentRelationField['column']['name'];
                $referencedField = $this->currentRelationField['column']['correspondent'];
                $columnMap = new RelationalColumnMap($columnName, $fieldName, $referencedField, $this->dataMap);
                $this->dataMap->addColumn($columnMap);
                $this->currentRelationField = [];
                break;
        }
    }
}
