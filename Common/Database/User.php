<?php

namespace Common\Database;

use Carbon\Carbon;
use JsonSerializable;
use PDO;

use function Common\driver;
use function Common\generateId;

class User implements JsonSerializable
{
    public const PLAN_FREE = 1;
    public const PLAN_BOOSTED = 2;
    public const PLAN_PRO_BOOSTED = 3;

    private int $id;
    private string $username;
    private string $password;
    private string $fname;
    private string $lname;
    private string $email;
    private Carbon $registered;
    private int $plan;

    /** @var Activity[] */
    private array $activities = [];

    public function getId(): int
    {
        return $this->id;
    }

    public function getPlan(): int
    {
        return $this->plan;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;
        return $this;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function setPassword(string $password, bool $isHash = false): self
    {
        $this->password = $isHash ? $password : password_hash($password, PASSWORD_DEFAULT);

        return $this;
    }

    public function setFname(string $fname): self
    {
        $this->fname = $fname;
        return $this;
    }

    public function setLname(string $lname): self
    {
        $this->lname = $lname;
        return $this;
    }

    public function setPlan(int $plan): self
    {
        if (!$this->isValidPlan($plan)) {
            throw new \InvalidArgumentException("Invalid plan: $plan");
        }

        $this->plan = $plan;
        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'fname' => $this->fname,
            'lname' => $this->lname,
            'registered' => $this->registered->getTimestamp(),
            'plan' => $this->plan,
        ];
    }

    public function isValidPlan(int $plan): bool
    {
        return in_array($plan, [self::PLAN_FREE, self::PLAN_BOOSTED, self::PLAN_PRO_BOOSTED]);
    }

    public function getFname(): string
    {
        return $this->fname;
    }

    public function getLname(): string
    {
        return $this->lname;
    }

    public function isPasswordCorrect(string $password): bool
    {
        return password_verify($password, $this->password);
    }

    public function getRegistered(?string $format = null): Carbon|string
    {
        return $format === null ? $this->registered : $this->registered->format($format);
    }

    public function setRegistered(Carbon $registered): self
    {
        $this->registered = $registered;
        return $this;
    }

    public function getActivities(): array
    {
        return $this->activities;
    }

    public function commit(): bool
    {
        $query = isset($this->id) ?
            '   UPDATE
                    users
                SET
                    username = :username,
                    email = :email,
                    password = :password,
                    registered = :registered,
                    fname = :fname,
                    lname = :lname
                WHERE
                    id = :id
            ' :
            '   INSERT INTO users (
                    id,
                    username,
                    email,
                    password,
                    registered,
                    fname,
                    lname
                ) VALUES (
                    :id,
                    :username,
                    :email,
                    :password,
                    :registered,
                    :fname,
                    :lname
                )
            '
        ;

        $stmt = driver()->prepare($query);

        if (!isset($this->id)) {
            $this->id = generateId();
        }

        return $stmt->execute([
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'password' => $this->password,
            'registered' => $this->registered->getTimestamp(),
            'fname' => $this->fname,
            'lname' => $this->lname
        ]);
    }

    public static function fetchById(int $id): ?self
    {
        $stmt = driver()->prepare('SELECT * FROM users WHERE id = :id');
        $stmt->execute(['id' => $id]);

        if ($stmt->rowCount() === 0) {
            return null;
        }

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return self::createFromDatabase(
            $row['id'],
            $row['username'],
            $row['password'],
            $row['email'],
            (new Carbon())->setTimestamp($row['registered']),
            $row['fname'],
            $row['lname'],
            $row['plan']
        );
    }

    public static function fetchByUsername(string $username): ?self
    {
        $stmt = driver()->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);

        if ($stmt->rowCount() === 0) {
            return null;
        }

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return self::createFromDatabase(
            $row['id'],
            $row['username'],
            $row['password'],
            $row['email'],
            (new Carbon())->setTimestamp($row['registered']),
            $row['fname'],
            $row['lname'],
            $row['plan']
        );
    }

    public static function fetchByEmail(string $email): ?self
    {
        $stmt = driver()->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);

        if ($stmt->rowCount() === 0) {
            return null;
        }

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return self::createFromDatabase(
            $row['id'],
            $row['username'],
            $row['password'],
            $row['email'],
            (new Carbon())->setTimestamp($row['registered']),
            $row['fname'],
            $row['lname'],
            $row['plan']
        );
    }

    public static function createFromDatabase(
        int $id,
        string $username,
        string $password,
        string $email,
        Carbon $carbon,
        string $fname,
        string $lname,
        int $plan
    ): self {
        $that = new self();

        $that->id = $id;
        $that->username = $username;
        $that->password = $password;
        $that->email = $email;
        $that->registered = $carbon;
        $that->fname = $fname;
        $that->lname = $lname;

        if (!$that->isValidPlan($plan)) {
            throw new \InvalidArgumentException("Invalid plan: $plan");
        }

        $that->plan = $plan;

        return $that;
    }

    /**
     * @return Activity[]
     */
    public function fetchActivities(): array
    {
        $stmt = driver()->prepare('SELECT * FROM activities WHERE user_id = :user_id ORDER BY date DESC');
        $stmt->execute(['user_id' => $this->id]);

        if ($stmt->rowCount() > 0) {
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($rows as $row) {
                $this->activities[] = Activity::createFromDatabase(
                    $row['id'],
                    $this,
                    $row['type'],
                    (new Carbon())->setTimestamp($row['date']),
                    json_decode($row['data'], true)
                );
            }
        }

        return $this->activities;
    }

    public function fetchQuestions()
    {
        $stmt = driver()->prepare('SELECT * FROM questions WHERE poster = :poster');
        $stmt->execute(['poster' => $this->id]);

        $questions = [];

        foreach ($stmt->fetchAll(PDO::FETCH_OBJ) as $row) {
            $questions[] = Question::createFromDatabase(
                $row->id,
                $row->title,
                $row->description,
                (new Carbon())->setTimestamp($row->posted),
                $this,
                json_decode($row->tags, true),
                $row->upvotes,
                $row->downvotes,
                $row->answered,
                $row->views
            );
        }

        return $questions;
    }

    public function getTotalUpVotes(): int
    {
        $stmt = driver()->prepare('SELECT SUM(upvotes) AS total FROM questions WHERE poster = :poster');
        $stmt->execute(['poster' => $this->id]);

        return (int) $stmt->fetch(PDO::FETCH_OBJ)->total;
    }

    public function getTotalDownVotes(): int
    {
        $stmt = driver()->prepare('SELECT SUM(downvotes) AS total FROM questions WHERE poster = :poster');
        $stmt->execute(['poster' => $this->id]);

        return (int) $stmt->fetch(PDO::FETCH_OBJ)->total;
    }

    public function getTotalQuestionsAsked(): int
    {
        $stmt = driver()->prepare('SELECT COUNT(*) AS total FROM questions WHERE poster = :poster');
        $stmt->execute(['poster' => $this->id]);

        return (int) $stmt->fetch(PDO::FETCH_OBJ)->total;
    }
}
