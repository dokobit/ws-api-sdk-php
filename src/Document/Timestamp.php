<?php
namespace Dokobit\Document;

use Dokobit\DocumentTypeProvider;
use Dokobit\FileFieldsTrait;
use Dokobit\QueryInterface;
use Dokobit\ResultInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Timestamp
 * Timestamps document's signatures.
 *
 * @package Dokobit\Document
 */
class Timestamp implements QueryInterface
{
    use FileFieldsTrait;

    /** @var string Possible values: pdf, adoc, mdoc */
    private $type;

    /** @var string file path */
    private $path;

    /**
     * @param string $type Possible values: pdf, adoc, mdoc
     * @param string $path
     */
    public function __construct($type, $path)
    {
        $this->type = $type;
        $this->path = $path;
    }

    /**
     * API action name, part of full API request url
     * @return string
     */
    public function getAction()
    {
        return 'timestamp';
    }

    /**
     * Field and values association used in query
     * @return array
     */
    public function getFields()
    {
        return [
            'type' => $this->type,
            'file' => $this->getFileFields($this->path),
        ];
    }

    /**
     * Result object for this query result
     * @return ResultInterface
     */
    public function createResult()
    {
        return new TimestampResult();
    }

    /**
     * Validation constraints for fields
     * @return array
     */
    public function getValidationConstraints()
    {
        return new Assert\Collection([
            'type'  => new Assert\Required([
                new Assert\NotBlank(),
                new Assert\Choice(choices: DocumentTypeProvider::getPrimaryDocumentTypes())
            ]),
            'file' => new Assert\Collection([
                'name' => new Assert\Required([
                    new Assert\NotBlank()
                ]),
                'content' => new Assert\Required([
                    new Assert\NotBlank(),
                ]),
                'digest' => new Assert\Required([
                    new Assert\NotBlank()
                ]),
            ]),
        ]);
    }

    /**
     * HTTP method to use
     * @return string
     */
    public function getMethod()
    {
        return QueryInterface::POST;
    }
}
