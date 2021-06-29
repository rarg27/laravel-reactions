<?php

namespace Qirolab\Laravel\Reactions\Traits;

use \Illuminate\Database\Eloquent\Model;
use Qirolab\Laravel\Reactions\Contracts\ReactableInterface;
use Qirolab\Laravel\Reactions\Events\OnDeleteReaction;
use Qirolab\Laravel\Reactions\Events\OnReaction;

trait Reacts
{
    /**
     * Reaction on reactable model.
     *
     * @param ReactableInterface $reactable
     * @param mixed              $type
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function reactTo(ReactableInterface $reactable, $type)
    {
        $reaction = $reactable->reactions()->where([
            'user_id' => $this->getKey(),
        ])->first();

        if (! $reaction) {
            return $this->storeReaction($reactable, $type);
        }

        if ($reaction->type == $type) {
            return $reaction;
        }

        $this->deleteReaction($reaction, $reactable);

        return $this->storeReaction($reactable, $type);
    }

    /**
     * Remove reaction from reactable model.
     *
     * @param ReactableInterface $reactable
     *
     * @return void
     */
    public function removeReactionFrom(ReactableInterface $reactable)
    {
        $reaction = $reactable->reactions()->where([
            'user_id' => $this->getKey(),
        ])->first();

        if (! $reaction) {
            return;
        }

        $this->deleteReaction($reaction, $reactable);
    }

    /**
     * Toggle reaction on reactable model.
     *
     * @param ReactableInterface $reactable
     * @param mixed              $type
     *
     * @return null|\Illuminate\Database\Eloquent\Model
     */
    public function toggleReactionOn(ReactableInterface $reactable, $type)
    {
        $reaction = $reactable->reactions()->where([
            'user_id' => $this->getKey(),
        ])->first();

        if (! $reaction) {
            return $this->storeReaction($reactable, $type);
        }

        $this->deleteReaction($reaction, $reactable);

        if ($reaction->type == $type) {
            return;
        }

        return $this->storeReaction($reactable, $type);
    }

    /**
     * Reaction on reactable model.
     *
     * @param ReactableInterface $reactable
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function ReactedOn(ReactableInterface $reactable)
    {
        return $reactable->reacted($this);
    }

    /**
     * Check is reacted on reactable model.
     *
     * @param ReactableInterface $reactable
     * @param mixed              $type
     *
     * @return bool
     */
    public function isReactedOn(ReactableInterface $reactable, $type = null)
    {
        $isReacted = $reactable->reactions()->where([
            'user_id' => $this->getKey(),
        ]);

        if ($type) {
            $isReacted->where([
                'type' => $type,
            ]);
        }

        return $isReacted->exists();
    }

    /**
     * Store reaction.
     *
     * @param  ReactableInterface                         $reactable
     * @param  mixed                                      $type
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function storeReaction(ReactableInterface $reactable, $type)
    {
        $reaction = $reactable->reactions()->create([
            'user_id' => $this->getKey(),
            'type' => $type,
        ]);

        event(new OnReaction($reactable, $reaction, $this));

        return $reaction;
    }

    /**
     * Delete reaction.
     *
     * @param  \Illuminate\Database\Eloquent\Model $reaction
     * @param  ReactableInterface $reactable
     * @return void
     */
    protected function deleteReaction(Model $reaction, ReactableInterface $reactable)
    {
        $response = $reaction->delete();

        event(new OnDeleteReaction($reactable, $reaction, $this));

        return $response;
    }
}
