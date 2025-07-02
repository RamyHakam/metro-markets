<?php

namespace App\Tests\Unit\Infrastructure\Cache;

use App\Infrastructure\Cache\PriceCacheSerializer;
use App\Domain\Model\Price;
use App\Domain\ValueObject\ProductId;
use App\Domain\ValueObject\PriceAmount;
use App\Application\DTO\PriceDTO;
use App\Domain\Factory\PriceFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use DateTimeImmutable;

class PriceCacheSerializerTest extends TestCase
{
    private SerializerInterface $serializer;
    private PriceCacheSerializer $adapter;

    protected function setUp(): void
    {
        $this->serializer = $this->createMock(SerializerInterface::class);
        $this->adapter = new PriceCacheSerializer($this->serializer);
    }

    public function testSerializeEncodesPriceAsJson(): void
    {
        $price = new Price(
            ProductId::create(100),
            'vendorA',
            PriceAmount::create(12.34),
            new DateTimeImmutable('2025-07-01T10:00:00Z')
        );

        $dto = PriceDTO::fromDomain($price);
        $json = '{"foo":"bar"}';

        // Expect serializer->serialize called with dto, format json and group
        $this->serializer
            ->expects($this->once())
            ->method('serialize')
            ->with(
                $this->equalTo($dto),
                'json',
                ['groups' => ['price:export']]
            )
            ->willReturn($json);

        $result = $this->adapter->serialize($price);
        $this->assertSame($json, $result);
    }

    public function testDeserializeBuildsPriceFromJson(): void
    {
        $json = '{"foo":"bar"}';
        $dto = new PriceDTO(
            productId: 200,
            vendorName: 'vendorB',
            price: 56.78,
            fetchedAt: new DateTimeImmutable('2025-07-02T12:34:56Z')
        );

        // Mock deserializer to return our DTO
        $this->serializer
            ->expects($this->once())
            ->method('deserialize')
            ->with(
                $json,
                PriceDTO::class,
                'json',
                ['groups' => ['price:export']]
            )
            ->willReturn($dto);

        $price = $this->adapter->deserialize($json);

        $expected = PriceFactory::create(
            $dto->productId,
            $dto->vendorName,
            $dto->price,
            $dto->fetchedAt
        );

        $this->assertEquals($expected, $price);
    }

    public function testSerializeThrowsExceptionOnFailure(): void
    {
        $price = $this->getMockBuilder(Price::class)
            ->setConstructorArgs([
                ProductId::create(123),
                'TestVendor',
                PriceAmount::create(15.99),
                new DateTimeImmutable('2025-07-02T12:00:00Z'),
            ])
            ->getMock();

        $exception = $this->createMock(ExceptionInterface::class);

        $this->serializer
            ->method('serialize')
            ->willThrowException($exception);

        $this->expectException(ExceptionInterface::class);
        $this->adapter->serialize($price);
    }

    public function testDeserializeThrowsExceptionOnFailure(): void
    {
        $json = '{}';
        $exception = $this->createMock(ExceptionInterface::class);

        $this->serializer
            ->method('deserialize')
            ->willThrowException($exception);

        $this->expectException(ExceptionInterface::class);
        $this->adapter->deserialize($json);
    }
}
