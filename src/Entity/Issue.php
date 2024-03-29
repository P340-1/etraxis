<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2017-2022 Artem Rodygin
//
//  This file is part of eTraxis.
//
//  You should have received a copy of the GNU General Public License
//  along with eTraxis. If not, see <https://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace App\Entity;

use App\Entity\Enums\SecondsEnum;
use App\Repository\IssueRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Issue.
 */
#[ORM\Entity(repositoryClass: IssueRepository::class)]
#[ORM\Table(name: 'issues')]
#[ORM\UniqueConstraint(fields: ['author', 'createdAt'])]
class Issue
{
    // Constraints.
    public const MAX_SUBJECT = 250;

    /**
     * Unique ID.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected int $id;

    /**
     * Subject of the issue.
     */
    #[ORM\Column(length: 250)]
    protected string $subject;

    /**
     * Current state.
     */
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    protected State $state;

    /**
     * Author of the issue.
     */
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    protected User $author;

    /**
     * Current responsible for the issue.
     */
    #[ORM\ManyToOne]
    protected ?User $responsible = null;

    /**
     * Original issue this issue was cloned from (when applicable).
     */
    #[ORM\ManyToOne(targetEntity: self::class)]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    protected ?self $origin = null;

    /**
     * Unix Epoch timestamp when the issue has been created.
     */
    #[ORM\Column]
    protected int $createdAt;

    /**
     * Unix Epoch timestamp when the issue has been changed last time.
     */
    #[ORM\Column]
    protected int $changedAt;

    /**
     * Unix Epoch timestamp when the issue has been closed, if so.
     */
    #[ORM\Column(nullable: true)]
    protected ?int $closedAt = null;

    /**
     * Unix Epoch timestamp when the issue will be resumed, if suspended.
     */
    #[ORM\Column(nullable: true)]
    protected ?int $resumesAt = null;

    /**
     * List of issue events.
     */
    #[ORM\OneToMany(mappedBy: 'issue', targetEntity: Event::class)]
    #[ORM\OrderBy(['createdAt' => 'ASC', 'id' => 'ASC'])]
    protected Collection $events;

    /**
     * List of issue watchers.
     */
    #[ORM\OneToMany(mappedBy: 'issue', targetEntity: Watcher::class)]
    protected Collection $watchers;

    /**
     * Creates new issue using specified template.
     */
    public function __construct(Template $template, User $author)
    {
        $state = $template->getInitialState();

        if (null === $state) {
            throw new \UnexpectedValueException('Template has no initial state');
        }

        $this->state  = $state;
        $this->author = $author;

        $this->createdAt = $this->changedAt = time();

        $this->events   = new ArrayCollection();
        $this->watchers = new ArrayCollection();
    }

    /**
     * Property getter.
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Returns full unique ID with template prefix.
     */
    public function getFullId(): string
    {
        return sprintf('%s-%03d', $this->state->getTemplate()->getPrefix(), $this->id);
    }

    /**
     * Property getter.
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * Property setter.
     */
    public function setSubject(string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Property getter.
     */
    public function getProject(): Project
    {
        return $this->state->getTemplate()->getProject();
    }

    /**
     * Property getter.
     */
    public function getTemplate(): Template
    {
        return $this->state->getTemplate();
    }

    /**
     * Property getter.
     */
    public function getState(): State
    {
        return $this->state;
    }

    /**
     * Property setter.
     */
    public function setState(State $state): self
    {
        if ($this->state->getTemplate() !== $state->getTemplate()) {
            throw new \UnexpectedValueException('Unknown state: '.$state->getName());
        }

        $this->state    = $state;
        $this->closedAt = $state->isFinal() ? time() : null;

        return $this;
    }

    /**
     * Property getter.
     */
    public function getAuthor(): User
    {
        return $this->author;
    }

    /**
     * Property getter.
     */
    public function getResponsible(): ?User
    {
        return $this->responsible;
    }

    /**
     * Property setter.
     */
    public function setResponsible(?User $responsible): self
    {
        $this->responsible = $responsible;

        return $this;
    }

    /**
     * Property getter.
     */
    public function getOrigin(): ?self
    {
        return $this->origin;
    }

    /**
     * Property setter.
     */
    public function setOrigin(?self $issue): self
    {
        if (null !== $issue && $issue->getTemplate() !== $this->getTemplate()) {
            throw new \UnexpectedValueException('Invalid origin: '.$issue->getFullId());
        }

        $this->origin = $issue;

        return $this;
    }

    /**
     * Whether the issue was cloned.
     */
    public function isCloned(): bool
    {
        return null !== $this->origin;
    }

    /**
     * Property getter.
     */
    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }

    /**
     * Returns number of days the issue remained or remains opened.
     */
    public function getAge(): int
    {
        return (int) ceil((($this->closedAt ?? time()) - $this->createdAt) / SecondsEnum::OneDay->value);
    }

    /**
     * Property getter.
     */
    public function getChangedAt(): int
    {
        return $this->changedAt;
    }

    /**
     * Updates the timestamp of when the issue has been changed.
     */
    public function touch(): void
    {
        $this->changedAt = time();
    }

    /**
     * Property getter.
     */
    public function getClosedAt(): ?int
    {
        return $this->closedAt;
    }

    /**
     * Whether the issue is closed.
     */
    public function isClosed(): bool
    {
        return null !== $this->closedAt;
    }

    /**
     * Whether the issue is critical (remains opened for too long).
     */
    public function isCritical(): bool
    {
        return !$this->isClosed()
            && null !== $this->state->getTemplate()->getCriticalAge()
            && $this->state->getTemplate()->getCriticalAge() < $this->getAge();
    }

    /**
     * Whether the issue is frozen (read-only).
     */
    public function isFrozen(): bool
    {
        return $this->isClosed()
            && null !== $this->state->getTemplate()->getFrozenTime()
            && $this->state->getTemplate()->getFrozenTime() < ceil((time() - $this->closedAt) / SecondsEnum::OneDay->value);
    }

    /**
     * Property getter.
     */
    public function getResumesAt(): ?int
    {
        return $this->resumesAt;
    }

    /**
     * Whether the issue is currently suspended.
     */
    public function isSuspended(): bool
    {
        return null !== $this->resumesAt && $this->resumesAt > time();
    }

    /**
     * Suspends the issue until specified timestamp.
     *
     * @param int $timestamp Unix Epoch timestamp
     */
    public function suspend(int $timestamp): void
    {
        $this->resumesAt = $timestamp;
    }

    /**
     * Resumes the issue if suspended.
     */
    public function resume(): void
    {
        $this->resumesAt = null;
    }

    /**
     * Property getter.
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    /**
     * Property getter.
     */
    public function getWatchers(): Collection
    {
        return $this->watchers->map(fn (Watcher $watcher) => $watcher->getUser());
    }
}
