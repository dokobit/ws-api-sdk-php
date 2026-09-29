<?php
namespace Dokobit\Document;

use Dokobit\DocumentTypeProvider;
use Dokobit\FileFieldsTrait;
use Dokobit\QueryInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Seal a signed document.
 */
class Seal implements QueryInterface
{
    use FileFieldsTrait;

    /** @var  string document type */
    private $type;

    /** @var  boolean add document timestamp */
    private $timestamp;

    /** @var  array document to be signed */
    private $document;

    /**
     * @param string $type string document type
     * @param array $document document to be signed
     * @param boolean $timestamp boolean add document timestamp
     */
    public function __construct(
        $type,
        array $document,
        $timestamp
    ) {
        $this->type = $type;
        $this->timestamp = $timestamp;
        $this->document = $document;
    }

    /**
     * Get fields
     * @return array
     */
    public function getFields()
    {
        $params = $this->document;

        if (isset($params['files'])) {
            $params['files'] = $this->getMultipleFileFields($params['files']);
        }

        $return = [
            'type' => $this->type,
            $this->type => $params
        ];

        if ($this->timestamp !== null) {
            $return['timestamp'] = $this->timestamp;
        }

        return $return;
    }

    /**
     * Validation constraints for request data validation
     * @return Assert\Collection
     */
    public function getValidationConstraints()
    {
        return new Assert\Collection([
            'type'  => new Assert\Required([
                new Assert\NotBlank(),
                new Assert\Choice(choices: DocumentTypeProvider::getAllDocumentTypes())
            ]),
            'timestamp' => new Assert\Required([
                new Assert\Type(type: 'bool')
            ]),
            $this->type => new Assert\Collection(fields: [], allowExtraFields: true),
        ]);
    }

    /**
     * Result object for this query result
     * @return SealResult
     */
    public function createResult()
    {
        return new SealResult();
    }

    /**
     * API action name, part of full url
     * @return string
     */
    public function getAction()
    {
        return 'seal';
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
