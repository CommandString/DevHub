<?php

namespace Common\Database;

use Carbon\Carbon;
use Common\Database\File;
use Common\Database\User;
use JsonSerializable;
use PDO;

use function Common\driver;
use function Common\generateId;

class Question implements JsonSerializable
{
    /** @var File[] */
    private array $files = [];
    private int $id;
    private string $title;
    private string $description;
    private Carbon $posted;
    private User $poster;
    private array $tags = [];
    private int $upVotes = 0;
    private int $downVotes = 0;
    private bool $answered = false;
    private int $views = 0;

    public static function fetchById(int $id): ?self
    {
        $stmt = driver()->prepare("SELECT * FROM questions WHERE id = :id");
        $stmt->execute(["id" => $id]);

        if ($stmt->rowCount() === 0) {
            return null;
        }

        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $post = new self();

        $post->id = $data["id"];
        $post
            ->setTitle($data["title"])
            ->setDescription($data["description"])
            ->setPosted((new Carbon())->setTimestamp($data["posted"]))
            ->setPoster(User::fetchById($data["poster"]))
            ->setTags(json_decode($data["tags"], true))
            ->setUpVotes($data["upvotes"])
            ->setDownVotes($data["downvotes"])
            ->setAnswered($data["answered"])
            ->setViews($data["views"])
        ;

        return $post;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getFiles(): array
    {
        return $this->files;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function setPosted(Carbon $posted): self
    {
        $this->posted = $posted;
        return $this;
    }

    public function setPoster(User $poster): self
    {
        $this->poster = $poster;
        return $this;
    }

    public function setTags(array $tags): self
    {
        $this->tags = $tags;
        return $this;
    }

    public function setUpVotes(int $upVotes): self
    {
        $this->upVotes = $upVotes;
        return $this;
    }

    public function setDownVotes(int $downVotes): self
    {
        $this->downVotes = $downVotes;
        return $this;
    }

    public function setAnswered(bool $answered): self
    {
        $this->answered = $answered;
        return $this;
    }

    public function setViews(int $views): self
    {
        $this->views = $views;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getPosted(?string $format = null): Carbon|string
    {
        return $format !== null ? $this->posted->format($format) : $this->posted;
    }

    public function getPoster(): User
    {
        return $this->poster;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function getUpVotes(): int
    {
        return $this->upVotes;
    }

    public function getDownVotes(): int
    {
        return $this->downVotes;
    }

    public function isAnswered(): bool
    {
        return $this->answered;
    }

    public function getViews(): int
    {
        return $this->views;
    }

    public function addFile(File $file): self
    {
        $this->files[] = $file;
        return $this;
    }

    public function addTag(string $tag): self
    {
        $this->tags[] = $tag;
        return $this;
    }

    public function upVote(): self
    {
        $this->upVotes++;
        return $this;
    }

    public function downVote(): self
    {
        $this->downVotes++;
        return $this;
    }

    public function answer(): self
    {
        $this->answered = true;
        return $this;
    }

    public function view(): self
    {
        $this->views++;
        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            "id" => $this->id,
            "title" => $this->title,
            "description" => $this->description,
            "posted" => $this->posted->getTimestamp(),
            "poster" => $this->poster,
            "tags" => $this->tags,
            "upVotes" => $this->upVotes,
            "downVotes" => $this->downVotes,
            "answered" => $this->answered,
            "views" => $this->views
        ];
    }

    public function commit(): bool
    {
        $query = isset($this->id) ?
            '   UPDATE
                    questions
                SET
                    title = :title,
                    description = :description,
                    posted = :posted,
                    poster = :poster,
                    tags = :tags,
                    upVotes = :upVotes,
                    downVotes = :downVotes,
                    answered = :answered,
                    views = :views
                WHERE
                    id = :id
            ' :
            '   INSERT INTO questions (
                    id,
                    title,
                    description,
                    posted,
                    poster,
                    tags,
                    upVotes,
                    downVotes,
                    answered,
                    views
                ) VALUES (
                    :id,
                    :title,
                    :description,
                    :posted,
                    :poster,
                    :tags,
                    :upVotes,
                    :downVotes,
                    :answered,
                    :views
                )
            '
        ;

        $stmt = driver()->prepare($query);

        if (!isset($this->id)) {
            $this->id = generateId();
        }

        return $stmt->execute([
            "id" => $this->id,
            "title" => $this->title,
            "description" => $this->description,
            "posted" => $this->posted->getTimestamp(),
            "poster" => $this->poster->getId(),
            "tags" => json_encode($this->tags),
            "upVotes" => $this->upVotes,
            "downVotes" => $this->downVotes,
            "answered" => $this->answered ? 1 : 0,
            "views" => $this->views
        ]);
    }

    /**
     * @return Comment[]
     */
    public function fetchComments(): array
    {
        $comments = [];

        $stmt = driver()->prepare("SELECT * FROM comments WHERE question = :question ORDER BY posted DESC");
        $stmt->execute(["question" => $this->id]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as $row) {
            $comments[] = Comment::createFromDatabase(
                $row["id"],
                User::fetchById($row["poster"]),
                $row["description"],
                (new Carbon())->setTimestamp($row["posted"]),
                $this,
                $row["isAnswer"],
                $row["upVotes"],
                $row["downVotes"],
                [] // TODO: Fetch files
            );
        }

        return $comments;
    }

    public function getTotalComments(): int
    {
        $stmt = driver()->prepare("SELECT COUNT(*) FROM comments WHERE question = :question");
        $stmt->execute(["question" => $this->id]);
        return (int) $stmt->fetchColumn();
    }

    public static function createFromDatabase(
        int $id,
        string $title,
        string $description,
        Carbon $posted,
        User $poster,
        array $tags,
        int $upVotes,
        int $downVotes,
        bool $answered,
        int $views
    ): self {
        $post = new self();

        $post->id = $id;
        $post
            ->setTitle($title)
            ->setDescription($description)
            ->setPosted($posted)
            ->setPoster($poster)
            ->setTags($tags)
            ->setUpVotes($upVotes)
            ->setDownVotes($downVotes)
            ->setAnswered($answered)
            ->setViews($views)
        ;

        return $post;
    }

    public function hasUserAlreadyUpVoted(User $user): bool
    { // TODO : Optimize this
        $activities = $user->fetchActivities();

        foreach ($activities as $activity) {
            if ($activity->fetchQuestion()->getId() === $this->id && $activity->getType() === Activity::UPVOTE_QUESTION) {
                return true;
            }
        }

        return false;
    }

    public function hasUserAlreadyDownVoted(User $user): bool
    { // TODO make this more efficient
        $activities = $user->fetchActivities();

        foreach ($activities as $activity) {
            if ($activity->fetchQuestion()->getId() === $this->id && $activity->getType() === Activity::DOWNVOTE_QUESTION) {
                return true;
            }
        }

        return false;
    }

    public function hasAlreadyVoted(User $user): bool
    {
        return $this->hasUserAlreadyUpVoted($user) || $this->hasUserAlreadyDownVoted($user);
    }
}