<?php

namespace Common\Database;

use Carbon\Carbon;
use CommandString\Utils\GeneratorUtils;
use Common\Database\User;

use function Common\driver;

class Comment
{
    private int $id;
    private User $poster;
    private string $description;
    private Carbon $posted;
    private Question $question;
    private bool $isAnswer;
    private int $upVotes;
    private int $downVotes;

    /** @var File[] */
    private array $files;

    public function setPoster(User $poster): self
    {
        $this->poster = $poster;
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

    public function setQuestion(Question $question): self
    {
        $this->question = $question;
        return $this;
    }

    public function setIsAnswer(bool $isAnswer): self
    {
        $this->isAnswer = $isAnswer;
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

    public function setFiles(array $files): self
    {
        $this->files = $files;
        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getPoster(): User
    {
        return $this->poster;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getPosted(): Carbon
    {
        return $this->posted;
    }

    public function getQuestion(): Question
    {
        return $this->question;
    }

    public function getIsAnswer(): bool
    {
        return $this->isAnswer;
    }

    public function getUpVotes(): int
    {
        return $this->upVotes;
    }

    public function getDownVotes(): int
    {
        return $this->downVotes;
    }

    public function getFiles(): array
    {
        return $this->files;
    }

    public function commit(): bool
    {
        $query = isset($this->id) ?
            'UPDATE comments SET poster = :poster, description = :description, posted = :posted, question = :question, isanswer = :isanswer, upvotes = :upvotes, downvotes = :downvotes WHERE id = :id' :
            'INSERT INTO comments (id, poster, description, posted, question, isanswer, upvotes, downvotes) VALUES (:id, :poster, :description, :posted, :question, :isanswer, :upvotes, :downvotes)'
        ;

        if (!isset($this->id)) {
            $this->id = (int) GeneratorUtils::uuid(16, range(1, 9));
        }

        $stmt = driver()->prepare($query);
        return $stmt->execute([
            'id' => $this->id,
            'poster' => $this->poster->getId(),
            'description' => $this->description,
            'posted' => $this->posted->getTimestamp(),
            'question' => $this->question->getId(),
            'isanswer' => $this->isAnswer ? 1 : 0,
            'upvotes' => $this->upVotes,
            'downvotes' => $this->downVotes
        ]);
    }

    public static function createFromDatabase(
        int $id,
        User $poster,
        string $description,
        Carbon $posted,
        Question $question,
        bool $isAnswer,
        int $upVotes,
        int $downVotes,
        array $files
    ): self {
        $comment = new self();
        $comment->id = $id;
        $comment->poster = $poster;
        $comment->description = $description;
        $comment->posted = $posted;
        $comment->question = $question;
        $comment->isAnswer = $isAnswer;
        $comment->upVotes = $upVotes;
        $comment->downVotes = $downVotes;
        $comment->files = $files;
        return $comment;
    }

    public static function fetchById(int $id): ?self
    {
        $stmt = driver()->prepare('SELECT * FROM comments WHERE id = :id');
        $stmt->execute(['id' => $id]);

        if ($stmt->rowCount() === 0) {
            return null;
        }

        $data = $stmt->fetch(\PDO::FETCH_ASSOC);

        return self::createFromDatabase(
            $data['id'],
            User::fetchById($data['poster']),
            $data['description'],
            (new Carbon())->setTimestamp($data['posted']),
            Question::fetchById($data['question']),
            $data['is_answer'],
            $data['upvotes'],
            $data['downvotes'],
            [] // TODO: Fetch files
        );
    }
}
