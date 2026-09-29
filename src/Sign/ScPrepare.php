<?php

namespace Dokobit\Sign;

use Dokobit\DocumentTypeProvider;
use Dokobit\FileFieldsTrait;
use Dokobit\QueryInterface;
use Dokobit\ResultInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ScPrepare implements QueryInterface
{
    use FileFieldsTrait;

    /** @var string base64_encode(certificate) */
    private $certificate;

    /** @var  string document type */
    private $type;

    /** @var  boolean add document timestamp */
    private $timestamp;

    /** @var string language LT|EN */
    private $language;

    /** @var  array document to be signed */
    private $document;

    /**
     * @param $certificate string base64_encode(certificate)
     * @param $type string document type
     * @param $timestamp boolean add document timestamp
     * @param $language string language LT|EN
     * @param $document array document to be signed
     */
    public function __construct($certificate, $type, $timestamp, $language, array $document)
    {
        $this->certificate = $certificate;
        $this->type = $type;
        $this->timestamp = $timestamp;
        $this->language = $language;
        $this->document = $document;
    }


    /**
     * API action name, part of full API request url
     * @return string
     */
    public function getAction()
    {
        return 'sc/prepare';
    }

    /**
     * Field and values association used in query
     * @return array
     */
    public function getFields()
    {
        $params = $this->document;
        if (isset($params['files'])) {
            $params['files'] = $this->getMultipleFileFields($params['files']);
        }
        
        return [
            'certificate' => $this->certificate,
            'type' => $this->type,
            'timestamp' => $this->timestamp,
            'language'  => $this->language,
            $this->type => $params
        ];
    }

    /**
     * Result object for this query result
     * @return ResultInterface
     */
    public function createResult()
    {
        return new ScPrepareResult();
    }

    /**
     * Validation constraints for fields
     * @return Assert\Collection
     */
    public function getValidationConstraints()
    {
        return new Assert\Collection([
            'certificate' => new Assert\Required([
                new Assert\NotBlank(),
            ]),
            'type'  => new Assert\Required([
                new Assert\NotBlank(),
                new Assert\Choice(choices: DocumentTypeProvider::getAllDocumentTypes())
            ]),
            'timestamp' => new Assert\Required([
                new Assert\Type(type: 'bool')
            ]),
            'language' => new Assert\Required([
                new Assert\Choice(choices: ['LT', 'EN'])
            ]),
            $this->type => new Assert\Collection(fields: [], allowExtraFields: true),
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
