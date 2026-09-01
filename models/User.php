<?php

class User
{
    private int $id;
    private string $name;
    private string $username;
    private string $email;
    private string $role;
    private string $status;

    public function __construct(
        int $id,
        string $name,
        string $username,
        string $email,
        string $role,
        string $status
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->username = $username;
        $this->email = $email;
        $this->role = $role;
        $this->status = $status;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getRole(): string
    {
        return $this->role;
    }
}