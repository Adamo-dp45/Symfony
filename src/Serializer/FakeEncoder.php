<?php

namespace App\Serializer;

use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;

class FakeEncoder implements EncoderInterface, DecoderInterface
{
    public const FORMAT = 'yaml';

    public function encode(mixed $data, string $format, array $context = []): string
    {
        // TODO: return your encoded data
        return '';
    }

    public function supportsEncoding(string $format): bool
    {
        return self::FORMAT === $format;
    }

    public function decode(string $data, string $format, array $context = []): mixed
    {
        // TODO: return your decoded data
        return '';
    }

    public function supportsDecoding(string $format): bool
    {
        return self::FORMAT === $format;
    }
}
