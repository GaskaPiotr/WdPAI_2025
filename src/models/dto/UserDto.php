<?php

class UserDto {
    public int $id;
    public string $email;
    public string $firstName;
    public string $lastName;
    public string $role;
    public int $roleId;
    public ?string $status;

    // Konstruktor
    public function __construct(
        int $id, 
        string $email, 
        string $firstName, 
        string $lastName, 
        string $role,
        int $roleId,
        string $status = null
    ) {
        $this->id = $id;
        $this->email = $email;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->role = $role;
        $this->roleId = $roleId;
        $this->status = $status;

    }
}