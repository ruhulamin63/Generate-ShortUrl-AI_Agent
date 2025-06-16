<?php 
namespace App\Neuron\Helper\Url;

trait ValidateCreateShorteneUrlTrait
{
    public function validateUrl(string $url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    public function validateCustomAlias(string $customAlias): bool
    {
        return preg_match('/^[a-zA-Z0-9-_]+$/', $customAlias);
    }

    public function validateDescription(string $description): bool
    {
        return strlen($description) <= 255;
    }
    public function validatePassword(string $password): bool
    {
        return strlen($password) >= 6;
    }
}