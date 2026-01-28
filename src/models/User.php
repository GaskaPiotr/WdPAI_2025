<?php

class User {
    private $id;
    private $email;
    private $password;
    private $name;
    private $surname;
    private $roleId;
    private $roleName;

    public function __construct(
        string $email, 
        string $password, 
        string $name, 
        string $surname, 
        int $roleId, 
        string $roleName,
        int $id = null
    ) {
        $this->email = $email;
        $this->password = $password;
        $this->name = $name;
        $this->surname = $surname;
        $this->roleId = $roleId;
        $this->roleName = $roleName; 
        $this->id = $id;
    }

    public function getId(): ?int { return $this->id; }
    public function getEmail(): string { return $this->email; }
    public function getPassword(): string { return $this->password; }
    public function getName(): string { return $this->name; }
    public function getSurname(): string { return $this->surname; }
    public function getRoleName(): string { return $this->roleName; }
    public function getRoleId(): int { return $this->roleId; }


    public function toDto(): UserDto {
        return new UserDto(
            $this->id,
            $this->email,
            $this->name,
            $this->surname,
            $this->roleName,
            $this->roleId,
            null
        );
    }
}