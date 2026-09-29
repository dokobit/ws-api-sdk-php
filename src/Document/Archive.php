<?php
namespace Dokobit\Document;

use Dokobit\DocumentTypeProvider;
use Dokobit\FileFieldsTrait;
use Dokobit\QueryInterface;
use Dokobit\ResultInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Archive
 * Prepares the document’s signatures for long-term document storage.
 * If document’s signatures are not prepared for long term storage and certificate is revoked or expired,
 * signed document is immediately rendered invalid.
 * Thus, archiving function is very important for all real-life scenarios
 * where signature’s meaning must be persisted through longer periods.
 *
 * @package Dokobit\Document
 */
class Archive implements QueryInterface
{
    use FileFieldsTrait;

    /** @var string Possible values: pdf, adoc, mdoc */
    private $type;

    /** @var string file path */
    private $path;

    /** @var array of signatures with required id key */
    private $signatures;

    /**
     * @param string $type Possible values: pdf, adoc, mdoc
     * @param string $path
     * @param array $signatures
     */
    public function __construct($type, $path, $signatures)
    {
        $this->type = $type;
        $this->path = $path;
        $this->signatures = $signatures;
    }

    /**
     * API action name, part of full API request url
     * @return string
     */
    public function getAction()
    {
        return 'archive';
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
            'signatures' => $this->signatures
        ];
    }

    /**
     * Result object for this query result
     * @return ResultInterface
     */
    public function createResult()
    {
        return new ArchiveResult();
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
            'signatures' => new Assert\Required([
                new Assert\Count(min: 1),
                new Assert\All([
                    new Assert\Collection([
                        'id' => new Assert\Required([
                            new Assert\NotBlank()
                        ]),
                    ])
                ])
            ])
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
