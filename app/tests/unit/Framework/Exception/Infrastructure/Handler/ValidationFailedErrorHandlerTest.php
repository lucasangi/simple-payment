<?php

declare(strict_types=1);

namespace SimplePayment\Tests\Framework\Exception\Infrastructure\Handler;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use SimplePayment\Framework\Exception\Domain\ErrorHandler;
use SimplePayment\Framework\Exception\Infrastructure\Handler\GenericErrorHandler;
use SimplePayment\Framework\Exception\Infrastructure\Handler\ValidationFailedErrorHandler;
use stdClass;
use Symfony\Component\Messenger\Exception\ValidationFailedException;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Throwable;

use function json_encode;

class ValidationFailedErrorHandlerTest extends TestCase
{
    private ErrorHandler $handler;

    protected function setUp(): void
    {
        $this->handler = new ValidationFailedErrorHandler();
    }

    public function testShouldIndicateCanHandleException(): void
    {
        $exception = new ValidationFailedException(new stdClass(), new ConstraintViolationList([]));
        $canDeal = $this->handler->canHandle($exception);

        $this->assertEquals(true, $canDeal);
    }

    public function testShouldIndicateCannotHandleException(): void
    {
        $exception = new RuntimeException();
        $canDeal = $this->handler->canHandle($exception);

        $this->assertEquals(false, $canDeal);
    }

    /**
     * @param string[] $expectedResponse
     *
     * @dataProvider supportedExceptionsProvider
     */
    public function testShouldHandleException(Throwable $exception, array $expectedResponse): void
    {
        $response = $this->handler->handle($exception);

        $this->assertJsonStringEqualsJsonString(
            json_encode($expectedResponse) ?: '',
            $response->getContent() ?: ''
        );
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testShouldHandleUnsupportedExceptionWithNextHandler(): void
    {
        $unsupportedException = new InvalidArgumentException();

        $next = new GenericErrorHandler();
        $handler = new ValidationFailedErrorHandler($next);

        $response = $handler->handle($unsupportedException);

        $this->assertJsonStringEqualsJsonString(
            json_encode(['detail' => 'Internal Server Error']) ?: '',
            $response->getContent() ?: ''
        );
        $this->assertEquals(500, $response->getStatusCode());
    }

    public function testShouldThrowExceptionOnTryHandleUnsupportedException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('This error cannot be handled.');

        $unsupportedException = new RuntimeException();
        $this->handler->handle($unsupportedException);
    }

    /** @return array<mixed> */
    public function supportedExceptionsProvider(): array
    {
        $exceptions = [
            0 => new ValidationFailedException(new stdClass(), new ConstraintViolationList([])),
            1 => $this->createValidationFailedException(
                [
                    [
                        'message' => 'The name must be at least 3 characters long',
                        'template' => 'The name must be at least {{ limit }} characters long',
                        'parameters' => [
                            '{{ limit }}' => 3,
                            '{{ value }}' => 'va',
                        ],
                        'property' => 'name',
                        'value' => 'va',
                    ],
                ],
            ),
            2 => $this->createValidationFailedException(
                [
                    [
                        'message' => 'The name must be at least 3 characters long',
                        'template' => 'The name must be at least {{ limit }} characters long',
                        'parameters' => [
                            'limit' => 3,
                            'value' => 'va',
                        ],
                        'property' => 'name',
                        'value' => 'va',
                    ],
                    [
                        'message' => 'This value is not a valid email address.',
                        'template' => 'This value is not a valid email address.',
                        'parameters' => ['value' => 'example.com'],
                        'property' => 'email',
                        'value' => 'example.com',
                    ],
                ],
            ),
        ];

        $expectedResponses = [
            0 => ['detail' => 'Validation Failed'],
            1 => [
                'detail' => 'Validation Failed',
                'violations' => [
                    [
                        'field' => 'name',
                        'message' => 'The name must be at least 3 characters long',
                        'parameters' => [
                            'limit' => 3,
                            'value' => 'va',
                        ],
                    ],
                ],
            ],
            2 => [
                'detail' => 'Validation Failed',
                'violations' => [
                    [
                        'field' => 'name',
                        'message' => 'The name must be at least 3 characters long',
                        'parameters' => [
                            'limit' => 3,
                            'value' => 'va',
                        ],
                    ],
                    [
                        'field' => 'email',
                        'message' => 'This value is not a valid email address.',
                        'parameters' => ['value' => 'example.com'],
                    ],
                ],
            ],
        ];

        return [
            'when there is just a detail information ' => [
                $exceptions[0],
                $expectedResponses[0],
            ],
            'when there is a full validation information' => [
                $exceptions[1],
                $expectedResponses[1],
            ],
            'where there are many violations' => [
                $exceptions[2],
                $expectedResponses[2],
            ],
        ];
    }

    /** @param array<mixed> $violations */
    protected function createValidationFailedException(array $violations): ValidationFailedException
    {
        $list = [];
        foreach ($violations as $violation) {
            $list[] = new ConstraintViolation(
                $violation['message'],
                $violation['template'],
                $violation['parameters'],
                '',
                $violation['property'],
                $violation['value']
            );
        }

        $constraints = new ConstraintViolationList($list);

        return new ValidationFailedException(new stdClass(), $constraints);
    }
}
