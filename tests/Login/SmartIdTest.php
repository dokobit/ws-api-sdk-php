<?php
namespace Dokobit\Tests\Login;

use Dokobit\Login\SmartId;
use Dokobit\QueryInterface;
use Dokobit\Tests\TestCase;

class SmartIdTest extends TestCase
{
    public function testGetFields()
    {
        $method = new SmartId('xxxxxxxxxxx', 'LT');
        
        $result = $method->getFields();

        $this->assertArrayHasKey('code', $result);
        $this->assertArrayHasKey('country', $result);
        $this->assertSame('xxxxxxxxxxx', $result['code']);
        $this->assertSame('LT', $result['country']);
    }

    public function testGetAction()
    {
        $method = new SmartId('', '');
        $this->assertSame('smartid/login', $method->getAction());
    }

    public function testGetMethod()
    {
        $method = new SmartId('', '');
        $this->assertSame(QueryInterface::POST, $method->getMethod());
    }

    public function testCreateResult()
    {
        $method = new SmartId('', '');
        $this->assertInstanceOf('Dokobit\Login\SmartIdResult', $method->createResult());
    }

    public function testHasValidationConstraints()
    {
        $method = new SmartId('', '');
        $collection = $method->getValidationConstraints();

        $this->assertInstanceOf(
            'Symfony\Component\Validator\Constraints\Collection',
            $collection
        );
    }
}
