<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

/**
 * A slide in the carousel at the top of the catalogue.
 *
 * Written by a manager, shown to shoppers. Kept apart from notices because the
 * two go in different places: a notice is a line in the bell, an advertisement
 * is a picture above the goods.
 */
class Advertisement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'eyebrow',
        'body',
        'image',
        'video',
        'poster',
        'link',
        'button_label',
        'position',
        'starts_on',
        'ends_on',
        'is_active',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'starts_on' => 'date',
            'ends_on' => 'date',
            'position' => 'integer',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Live: switched on, and inside its date window if it has one.
     */
    public function scopeLive(Builder $query): Builder
    {
        $today = now()->toDateString();

        return $query->where('is_active', true)
            ->where(fn (Builder $q) => $q->whereNull('starts_on')->orWhereDate('starts_on', '<=', $today))
            ->where(fn (Builder $q) => $q->whereNull('ends_on')->orWhereDate('ends_on', '>=', $today));
    }

    public function isLive(): bool
    {
        return static::query()->live()->whereKey($this->id)->exists();
    }

    /**
     * Whether there is anything to actually show.
     *
     * A slide with neither a picture nor a video falls back to the store's own
     * colours rather than a blank frame, so it is still worth showing.
     */
    public function hasMedia(): bool
    {
        return $this->hasImage() || $this->hasVideo();
    }

    public function hasImage(): bool
    {
        return filled($this->image) && Storage::disk('public')->exists($this->image);
    }

    /**
     * A video is only offered once the file is really there, so a missing
     * upload cannot leave a black rectangle at the top of the page.
     */
    public function hasVideo(): bool
    {
        return filled($this->video) && Storage::disk('public')->exists($this->video);
    }

    public function imageUrl(): ?string
    {
        return $this->hasImage() ? Storage::disk('public')->url($this->image) : null;
    }

    public function videoUrl(): ?string
    {
        return $this->hasVideo() ? Storage::disk('public')->url($this->video) : null;
    }

    public function posterUrl(): ?string
    {
        return filled($this->poster) && Storage::disk('public')->exists($this->poster)
            ? Storage::disk('public')->url($this->poster)
            : null;
    }

    /**
     * The shape the carousel component expects.
     *
     * @return array<string, mixed>
     */
    public function toSlide(): array
    {
        return [
            'type' => $this->hasVideo() ? 'video' : 'image',
            'src' => $this->videoUrl() ?? $this->imageUrl(),
            'poster' => $this->posterUrl(),
            'alt' => $this->title,
            'title' => $this->title,
            'eyebrow' => $this->eyebrow,
            'text' => $this->body,
            'url' => $this->link,
            'label' => $this->button_label ?: 'See more',
            // Tells the component there is no picture, so it can drop the
            // scrim rather than laying it over an empty gradient.
            'plain' => ! $this->hasMedia(),
        ];
    }

    /**
     * A one-line description of when this one is showing, for the list.
     */
    public function windowLabel(): string
    {
        if (! $this->is_active) {
            return 'Switched off';
        }

        return match (true) {
            $this->starts_on && $this->ends_on => 'From '.$this->starts_on->format('j M').' until '.$this->ends_on->format('j M Y'),
            $this->starts_on => 'From '.$this->starts_on->format('j M Y'),
            $this->ends_on => 'Until '.$this->ends_on->format('j M Y'),
            default => 'Always on',
        };
    }
}
