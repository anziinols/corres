<?php

namespace App\Helpers;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class UuidHelper
{
    public static function generate(): string
    {
        return Uuid::uuid7()->toString();
    }

    public static function generateBinary(): string
    {
        return Uuid::uuid7()->getBytes();
    }

    public static function toBinary(string $uuid): string
    {
        return Uuid::fromString($uuid)->getBytes();
    }

    public static function toString(string $binary): string
    {
        return Uuid::fromBytes($binary)->toString();
    }

    public static function isValid(string $uuid): bool
    {
        return Uuid::isValid($uuid);
    }

    public static function isUuidV7(string $uuid): bool
    {
        try {
            $uuidObj = Uuid::fromString($uuid);
            return $uuidObj->getVersion() === 7;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function getTimestamp(string $uuid): int
    {
        $uuidObj = Uuid::fromString($uuid);
        if ($uuidObj->getVersion() !== 7) {
            throw new \InvalidArgumentException('UUID is not version 7');
        }

        $hex = str_replace('-', '', $uuid);
        $timestampHex = substr($hex, 0, 12);
        return hexdec($timestampHex);
    }
}
