<?php

namespace App\VoteApp;

class ClassUser
{
    private string $firstName;
    private string $lastName;
    private string $username;
    private string $email;
    private string $password;
    private string $role;

public function __construct(
    string $firstName = "John",
    string $lastName = "Doe",
    string $username = "JohnDoe1234",
    string $email = "john@doe.com",
    string $password = "password1234",
    string $role = "admin"
)
{
    $this->firstName = $firstName;
    $this->lastName = $lastName;
    $this->username = $username;
    $this->email = $email;
    $this->password = $password;
    $this->role = $role;
}

/*First Name functions*/
public function getFirstName(): string
{
    return $this->firstName;
}

public function setFirstName(string $firstName): void
{
    $this->firstName = $firstName;
}

/*Last Name functions*/
public function getLastName(): string
{
    return $this->lastName;
}

public function setLastName(string $lastName): void
{
    $this->lastName = $lastName;
}

/*Username functions */
public function getUsername(): string
{
    return $this->username;
}

public function setUsername(string $username): void
{
    $this->username = $username;
}

/*Email functions */
public function getEmail(): string
{
    return $this->email;
}

public function setEmail(string $email): void
{
    $this->email = $email;
}

/*Role functions */
public function getRole(): string
{
    return $this->role;
}

public function setRole(string $role): void
{
    $this->role = $role;
}

/*password functions */
public function getPassword(): string
{
    return $this->password;
}

public function setPassword(string $password): void
{
    $this->password = $password;
}
}