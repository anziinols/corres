<?php

use App\Helpers\UuidHelper;

if (!function_exists('uuid_generate')) {
    function uuid_generate(): string
    {
        return UuidHelper::generate();
    }
}

if (!function_exists('uuid_binary')) {
    function uuid_binary(): string
    {
        return UuidHelper::generateBinary();
    }
}

if (!function_exists('uuid_to_bin')) {
    function uuid_to_bin(string $uuid): string
    {
        return UuidHelper::toBinary($uuid);
    }
}

if (!function_exists('bin_to_uuid')) {
    function bin_to_uuid(string $binary): string
    {
        return UuidHelper::toString($binary);
    }
}
