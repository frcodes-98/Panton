<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Document;
use App\Models\Folder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AreaLibraryController extends Controller
{
    public function update(Request $request, Area $area): JsonResponse
    {
        $data = $request->validate([
            'folders' => ['present', 'array'],
            'folders.*.id' => ['required'],
            'folders.*.parentId' => ['nullable'],
            'folders.*.name' => ['required', 'string', 'max:120'],
            'folders.*.security' => ['nullable', 'array'],
            'files' => ['present', 'array'],
            'files.*.id' => ['required'],
            'files.*.folderId' => ['nullable'],
            'files.*.reference' => ['required', 'string', 'max:80'],
            'files.*.description' => ['required', 'string', 'max:255'],
            'files.*.rev' => ['nullable', 'string', 'max:20'],
            'files.*.effective' => ['nullable', 'string', 'max:20'],
            'files.*.workflow' => ['nullable', 'string', 'max:40'],
            'files.*.archived' => ['nullable', 'boolean'],
            'files.*.versions' => ['nullable', 'array'],
            'history' => ['present', 'array'],
            'history.*.text' => ['required', 'string', 'max:255'],
            'areaSecurity' => ['nullable', 'array'],
        ]);

        $idMap = DB::transaction(function () use ($area, $data): array {
            $area->security = $data['areaSecurity'] ?? ['Admin', 'Editor', 'View Only'];
            $area->save();

            $area->documents()->delete();
            $area->folders()->update(['parent_id' => null]);
            $area->folders()->delete();
            $area->activities()->delete();

            $folderMap = [];
            $fileMap = [];
            $pending = $data['folders'];
            $guard = 0;

            while ($pending !== [] && $guard < 50) {
                $guard++;
                $next = [];

                foreach ($pending as $folder) {
                    $parentKey = $folder['parentId'] ?? null;
                    if ($parentKey !== null && $parentKey !== '' && ! array_key_exists((string) $parentKey, $folderMap)) {
                        $next[] = $folder;

                        continue;
                    }

                    $created = $area->folders()->create([
                        'parent_id' => $parentKey === null || $parentKey === '' ? null : $folderMap[(string) $parentKey],
                        'name' => $folder['name'],
                        'security' => $folder['security'] ?? ['Admin', 'Editor', 'View Only'],
                    ]);
                    $folderMap[(string) $folder['id']] = $created->id;
                }

                if (count($next) === count($pending)) {
                    break;
                }

                $pending = $next;
            }

            foreach ($data['files'] as $file) {
                $folderKey = $file['folderId'] ?? null;
                $document = $area->documents()->create([
                    'folder_id' => $folderKey === null || $folderKey === '' ? null : ($folderMap[(string) $folderKey] ?? null),
                    'reference' => $file['reference'],
                    'description' => $file['description'],
                    'rev' => $file['rev'] ?: '0',
                    'effective_on' => $file['effective'] ?: null,
                    'workflow' => $file['workflow'] ?: 'Draft',
                    'archived' => (bool) ($file['archived'] ?? false),
                ]);

                foreach ($file['versions'] ?? [] as $version) {
                    $document->versions()->create([
                        'rev' => $version['rev'] ?? '',
                        'effective_on' => ($version['effective'] ?? null) ?: null,
                    ]);
                }

                $fileMap[(string) $file['id']] = $document->id;
            }

            foreach (array_reverse(array_slice($data['history'], 0, 40)) as $entry) {
                $area->activities()->create(['body' => $entry['text']]);
            }

            return [
                'folders' => $folderMap,
                'files' => $fileMap,
            ];
        });

        return response()->json([
            ...$area->fresh()->toLibrary(),
            'idMap' => $idMap,
        ]);
    }
}
