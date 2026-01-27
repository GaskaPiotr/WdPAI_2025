<?php

class User {
    private $id;
    private $email;
    private $password;
    private $name;
    private $surname;
    private $roleId;

    public function __construct(string $email, string $password, string $name, string $surname, int $roleId, int $id = null) {
        $this->email = $email;
        $this->password = $password;
        $this->name = $name;
        $this->surname = $surname;
        $this->roleId = $roleId;
        $this->id = $id;
    }

    public function getId(): ?int { return $this->id; }
    public function getEmail(): string { return $this->email; }
    public function getPassword(): string { return $this->password; }
    public function getName(): string { return $this->name; }
    public function getSurname(): string { return $this->surname; }
    public function getRoleId(): int { return $this->roleId; }
}