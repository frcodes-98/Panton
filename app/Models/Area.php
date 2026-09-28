<?php

namespace App\Models;

use App\Data\DemoCatalog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Area extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'icon',
        'repository',
        'security',
        'approver_ids',
        'control_ids',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'repository' => 'boolean',
            'security' => 'array',
            'approver_ids' => 'array',
            'control_ids' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Area $area): void {
            if (! $area->slug) {
                $area->slug = DemoCatalog::slug($area->name);
            }
        });
    }

    public function folders(): HasMany
    {
        return $this->hasMany(Folder::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(AreaActivity::class);
    }

    public function staff(): BelongsToMany
    {
        return $this->belongsToMany(Staff::class);
    }

    public function toListing(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'repository' => $this->repository,
            'approver_ids' => $this->approver_ids ?? [],
            'control_ids' => $this->control_ids ?? [],
        ];
    }

    public function toLibrary(): array
    {
        $folders = $this->folders()->orderBy('name')->get();
        $documents = $this->documents()->with('versions')->orderBy('reference')->get();

        return [
            'name' => $this->name,
            'slug' => $this->slug,
            'areaSecurity' => $this->security ?? ['Admin', 'Editor', 'View Only'],
            'folders' => $folders->map(fn (Folder $folder) => [
                'id' => $folder->id,
                'parentId' => $folder->parent_id,
                'name' => $folder->name,
                'security' => $folder->security ?? ['Admin', 'Editor', 'View Only'],
            ])->values()->all(),
            'files' => $documents->map(fn (Document $document) => [
                'id' => $document->id,
                'folderId' => $document->folder_id,
                'reference' => $document->reference,
                'description' => $document->description,
                'rev' => $document->rev,
                'effective' => optional($document->effective_on)->toDateString() ?? '',
                'workflow' => $document->workflow,
                'archived' => $document->archived,
                'versions' => $document->versions->map(fn (DocumentVersion $version) => [
                    'rev' => $version->rev,
                    'effective' => optional($version->effective_on)->toDateString() ?? '',
                ])->values()->all(),
            ])->values()->all(),
            'history' => $this->activities()->orderByDesc('id')->limit(40)->get()->map(fn (AreaActivity $activity) => [
                'id' => $activity->id,
                'text' => $activity->body,
                'at' => $activity->created_at?->timezone(config('app.timezone'))->format('n/j/Y, g:i:s A') ?? '',
            ])->values()->all(),
        ];
    }
}
