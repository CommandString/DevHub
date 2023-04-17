<?php

namespace Common\Database;

use Carbon\Carbon;
use CommandString\Utils\GeneratorUtils;
use PDO;
use stdClass;

use function Common\driver;

class Activity
{
    public const CHANGE_USERNAME = 1;
    public const CHANGE_PROFILE_PICTURE = 2;
    public const UPVOTE_POST = 3;
    public const DOWNVOTE_POST = 4;
    public const ANSWER_POST = 5;
    public const CREATE_POST = 6;
    public const CREATE_COMMENT = 7;

    private int $id;
    private User $user;
    private int $type;
    private Carbon $date;
    private array $data;

    public function getId(): int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function setType(int $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function setDate(Carbon $date): self
    {
        $this->date = $date;
        return $this;
    }

    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function commit(): bool
    {
        $query = !isset($this->id) ?
            "INSERT INTO activities (id, user_id, type, date, data) VALUES (:id, :user_id, :type, :date, :data)" :
            "UPDATE activities SET user_id = :user_id, type = :type, date = :date, data = :data WHERE id = :id"
        ;

        $stmt = driver()->prepare($query);

        if (!isset($this->id)) {
            $this->id = (int)GeneratorUtils::uuid(16, range(0, 9));
        }

        return $stmt->execute([
            'id' => $this->id,
            'user_id' => $this->user->getId(),
            'type' => $this->type,
            'date' => $this->date->getTimestamp(),
            'data' => json_encode($this->data)
        ]);
    }

    public static function fetchById(int $id): ?self
    {
        $query = "SELECT * FROM activities WHERE id = :id";
        $stmt = driver()->prepare($query);
        $stmt->execute(['id' => $id]);

        if ($stmt->rowCount() === 0) {
            return null;
        }

        $row = $stmt->fetch(PDO::FETCH_OBJ);

        return self::createFromDatabase(
            $row->id,
            User::fetchById($row->user_id),
            $row->type,
            (new Carbon())->setTimestamp($row->date),
            json_decode($row->data, true)
        );
    }

    public static function createFromDatabase(int $id, User $user, int $type, Carbon $date, array $data): self
    {
        $activity = new self();

        $activity->id = $id;
        $activity->user = $user;
        $activity->type = $type;
        $activity->date = $date;
        $activity->data = $data;

        return $activity;
    }
}
