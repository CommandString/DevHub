<?php

namespace Common\Database;

use CommandString\Utils\GeneratorUtils;

use function Common\driver;

class Newsletter {
    private string $email;
    private int $id;

    public function __construct(string $email, ?int $id = null) {
        $this->email = $email;

        if ($id !== null) {
            $this->id = $id;
        }
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function getId(): int {
        return $this->id;
    }

    public function setEmail(string $email): void {
        $this->email = $email;
    }

    public function setId(int $id): void {
        $this->id = $id;
    }

    public static function fetchByEmail(string $email): ?Newsletter {
        $stmt = driver()->prepare("SELECT * FROM newsletters WHERE email = :email");
        $stmt->execute([":email" => $email]);

        $result = $stmt->fetch();

        if ($result === false) {
            return null;
        }

        return new Newsletter($result["email"], (int) $result["id"]);
    }

    public function commit(): bool
    {
        # using pdo
        $query = isset($this->id) ? "UPDATE newsletters SET email = :email WHERE id = :id" : "INSERT INTO newsletters (id, email) VALUES (:id, :email)";

        $stmt = driver()->prepare($query);

        if (!isset($this->id)) {
            $this->id = (int) GeneratorUtils::uuid(16, range(0, 9));
        }

        return $stmt->execute([
            ":id" => $this->id,
            ":email" => $this->email
        ]);
    }
}