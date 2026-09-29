<?php
namespace Dokobit\Sign;

use Dokobit\DocumentTypeProvider;
use Dokobit\FileFieldsTrait;
use Dokobit\QueryInterface;
use Dokobit\Validator\Constraints\Code;
use Dokobit\Validator\Constraints\Phone;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Sign document using mobile ID.
 */
class Mobile implements QueryInterface
{
    use FileFieldsTrait;

    /** @var string document type */
    private $type;

    /** @var string user phone number */
    private $phone;

    /** @var string user personal code */
    private $code;

    /** @var array document to be signed */
    private $document;

    /** @var string language used for login message */
    private $language;

    /** @var string custom login message to be displayed in mobile phone */
    private $message;

    /** @var boolean add document timestamp */
    private $timestamp;

    /**
     * @param string $type document type
     * @param string $phone user phone number
     * @param string $code user personal code
     * @param array $document document to be signed
     * @param string $language language used for login message
     * @param string $message custom login message to be displayed in mobile phone
     * @param boolean $timestamp add document timestamp
     */
    public function __construct(
        $type,
        $phone,
        $code,
        array $document,
        $language = null,
        $message = null,
        $timestamp = null
    ) {
        $this->type = $type;
        $this->phone = $phone;
        $this->code = $code;
        $this->document = $document;
        $this->language = $language;
        $this->message = $message;
        $this->timestamp = $timestamp;
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

        $return = [
            'type' => $this->type,
            'phone' => $this->phone,
            'code' => $this->code,
            $this->type => $params,
        ];

        if ($this->timestamp !== null) {
            $return['timestamp'] = $this->timestamp;
        }

        if ($this->language !== null) {
            $return['language'] = $this->language;
        }

        if ($this->message !== null) {
            $return['message'] = $this->message;
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
            'type' => new Assert\Required([
                new Assert\NotBlank(),
                new Assert\Choice(choices: DocumentTypeProvider::getAllDocumentTypes()),
            ]),
            'phone' => new Assert\Required([
                new Assert\NotBlank(),
                new Phone(),
            ]),
            'code' => new Assert\Required([
                new Assert\NotBlank(),
                new Code()
            ]),
            $this->type => new Assert\Collection(fields: [], allowExtraFields: true),
            'language' => new Assert\Optional(),
            'message' => new Assert\Optional(),
            'timestamp' => new Assert\Optional(),
        ]);
    }

    /**
     * Result object for this query result
     * @return MobileResult
     */
    public function createResult()
    {
        return new MobileResult();
    }

    /**
     * API action name, part of full API request url
     * @return string
     */
    public function getAction()
    {
        return 'mobile/sign';
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
