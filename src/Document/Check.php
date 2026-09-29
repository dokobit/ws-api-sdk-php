<?php
namespace Dokobit\Document;

use Dokobit\DocumentTypeProvider;
use Dokobit\FileFieldsTrait;
use Dokobit\QueryInterface;
use Dokobit\ResultInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Check
 * This method returns PDF, ADoc or MDoc file with meta data.
 * Can be used to fetch information about document’s signatures.
 *
 * @package Dokobit\Document
 */
class Check implements QueryInterface
{
    use FileFieldsTrait;

    const VALIDATION_POLICY_QES = 'qes';
    const VALIDATION_POLICY_AES = 'aes';

    /** @var string Possible values: pdf, adoc, mdoc */
    private $type;

    /** @var string file path */
    private $path;

    /** @var string Possible values: qes, aes */
    private $validationPolicy;

    /**
     * @param string $type
     * @param string $path
     * @param string $validationPolicy
     */
    public function __construct($type, $path, $validationPolicy = self::VALIDATION_POLICY_QES)
    {
        $this->type = $type;
        $this->path = $path;
        $this->validationPolicy = $validationPolicy;
    }

    /**
     * API action name, part of full API request url
     * @return string
     */
    public function getAction()
    {
        return 'check';
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
            'validation_policy' => $this->validationPolicy
        ];
    }

    /**
     * Result object for this query result
     * @return ResultInterface
     */
    public function createResult()
    {
        return new CheckResult();
    }

    /**
     * Validation constraints for fields
     * @return Assert\Collection
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
            'validation_policy' => new Assert\Optional([
                new Assert\Choice(choices: $this->getValidationPolicies())
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

    /**
     * @return array
     */
    public function getValidationPolicies()
    {
        return [self::VALIDATION_POLICY_QES, self::VALIDATION_POLICY_AES];
    }
}
