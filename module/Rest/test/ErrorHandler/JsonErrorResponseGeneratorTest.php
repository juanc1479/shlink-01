<?php
declare(strict_types=1);

namespace ShlinkioTest\Shlink\Rest\ErrorHandler;

use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Rest\ErrorHandler\JsonErrorResponseGenerator;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;

class JsonErrorResponseGeneratorTest extends TestCase
{
    /** @var JsonErrorResponseGenerator */
    private $errorHandler;

    public function setUp(): void
    {
        $this->errorHandler = new JsonErrorResponseGenerator();
    }

    /**
     * @test
     */
    public function noErrorStatusReturnsInternalServerError()
    {
        $response = $this->errorHandler->__invoke(null, new ServerRequest(), new Response());
        $this->assertInstanceOf(Response\JsonResponse::class, $response);
        $this->assertEquals(500, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function errorStatusReturnsThatStatus()
    {
        $response = $this->errorHandler->__invoke(
            null,
            new ServerRequest(),
            (new Response())->withStatus(405)
        );
        $this->assertInstanceOf(Response\JsonResponse::class, $response);
        $this->assertEquals(405, $response->getStatusCode());
    }
}
