<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Core\Util;

use Fig\Http\Message\RequestMethodInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Shlinkio\Shlink\Core\Exception\InvalidUrlException;
use Shlinkio\Shlink\Core\Util\UrlValidator;
use Zend\Diactoros\Response;

class UrlValidatorTest extends TestCase
{
    private UrlValidator $urlValidator;
    private ObjectProphecy $httpClient;

    public function setUp(): void
    {
        $this->httpClient = $this->prophesize(ClientInterface::class);
        $this->urlValidator = new UrlValidator($this->httpClient->reveal());
    }

    /** @test */
    public function exceptionIsThrownWhenUrlIsInvalid(): void
    {
        $request = $this->httpClient->request(Argument::cetera())->willThrow(ClientException::class);

        $request->shouldBeCalledOnce();
        $this->expectException(InvalidUrlException::class);

        $this->urlValidator->validateUrl('http://foobar.com/12345/hello?foo=bar');
    }

    /** @test */
    public function expectedUrlIsCalledWhenTryingToVerify(): void
    {
        $expectedUrl = 'http://foobar.com';

        $request = $this->httpClient->request(
            RequestMethodInterface::METHOD_GET,
            $expectedUrl,
            [RequestOptions::ALLOW_REDIRECTS => ['max' => 15]]
        )->willReturn(new Response());

        $this->urlValidator->validateUrl($expectedUrl);

        $request->shouldHaveBeenCalledOnce();
    }
}
