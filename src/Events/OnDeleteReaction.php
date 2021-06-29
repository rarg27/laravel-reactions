<?php

namespace Qirolab\Laravel\Reactions\Events;

use Illuminate\Database\Eloquent\Model;
use Qirolab\Laravel\Reactions\Contracts\ReactableInterface;
use Qirolab\Laravel\Reactions\Contracts\ReactsInterface;

class OnDeleteReaction
{
    /**
     * The reactable model.
     *
     * @var ReactableInterface
     */
    public $reactable;

    /**
     * User who reacted on model.
     *
     * @var ReactsInterface
     */
    public $reactBy;

    /**
     * Reaction model.
     *
     * @var Model
     */
    public $reaction;

    /**
     * Create a new event instance.
     *
     * @param ReactableInterface $reactable
     * @param Model $reaction
     * @param ReactsInterface    $reactBy
     *
     * @return void
     */
    public function __construct(ReactableInterface $reactable, Model $reaction, ReactsInterface $reactBy)
    {
        $this->reactable = $reactable;
        $this->reaction = $reaction;
        $this->reactBy = $reactBy;
    }
}
