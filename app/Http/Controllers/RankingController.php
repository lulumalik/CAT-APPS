<?php

namespace App\Http\Controllers;

use App\Models\BimbleClass;
use App\Models\ManualRankingEntry;
use App\Models\RegistrationProgress;
use App\Models\TestDefinition;
use App\Models\TestSubmission;
use App\Models\User;
use App\Services\AutoStudentReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class RankingController extends Controller
{
    public function categories()
    {
        return response()->json([
            'groups' => config('rankings.groups', []),
            'class_subject_guide' => config('rankings.class_subject_guide', []),
        ]);
    }

    public function filters(Request $request)
    {
        if (! Schema::hasTable('bimble_classes')) {
            return response()->json(['classes' => [], 'cohorts' => []]);
        }

        $classes = BimbleClass::query()
            ->orderBy('name')
            ->get(['id', 'name', 'class_code', 'academic_period'])
            ->map(fn ($c) => [
                'id' => $c->id,
                'name' => $c->name,
                'class_code' => $c->class_code,
                'academic_period' => $c->academic_period,
            ]);

        $cohorts = Schema::hasTable('registration_progress')
            ? RegistrationProgress::query()
                ->whereNotNull('administration_data')
                ->get(['administration_data'])
                ->map(function ($row) {
                    $data = $row->administration_data ?? [];

                    return $data['angkatan'] ?? $data['cohort'] ?? $data['batch'] ?? null;
                })
                ->filter()
                ->unique()
                ->values()
            : collect();

        $fromClasses = $classes->pluck('academic_period')->filter()->unique();
        $cohorts = $cohorts->merge($fromClasses)->unique()->sort()->values();

        return response()->json([
            'classes' => $classes,
            'cohorts' => $cohorts,
        ]);
    }

    public function index(Request $request)
    {
        $validated = $this->validateRankingContext($request);

        $group = $this->findGroup($validated['group_id']);
        if (! $group) {
            return response()->json(['message' => 'Kategori tidak ditemukan.'], 404);
        }

        $sub = $this->findSubcategory($group, $validated['subcategory_id']);
        if (! $sub) {
            return response()->json(['message' => 'Subkategori tidak ditemukan.'], 404);
        }

        $userIds = $this->resolveUserIds($validated['scope'], $validated['class_id'] ?? null, $validated['cohort'] ?? null);

        $auto = ($group['source'] ?? '') === 'physical'
            ? $this->buildPhysicalLeaderboard($sub, $userIds)
            : $this->buildAcademicLeaderboard($sub, $userIds);

        $manual = $this->buildManualLeaderboard($validated, $sub);
        $entries = $this->mergeLeaderboards($auto, $manual, $sub);

        return response()->json([
            'scope' => $validated['scope'],
            'group' => [
                'id' => $group['id'],
                'label' => $group['label'],
                'scoring_mode' => $group['scoring_mode'] ?? 'manual',
            ],
            'subcategory' => [
                'id' => $sub['id'],
                'label' => $sub['label'],
                'unit' => $sub['unit'] ?? null,
                'sort' => $sub['sort'] ?? 'desc',
            ],
            'class_guide' => config('rankings.class_subject_guide.'.$group['id']),
            'entries' => $entries,
            'total' => count($entries),
        ]);
    }

    public function manualList(Request $request)
    {
        $validated = $this->validateRankingContext($request);

        if (! Schema::hasTable('manual_ranking_entries')) {
            return response()->json(['items' => []]);
        }

        $entries = ManualRankingEntry::query()
            ->where($this->manualContextWhere($validated))
            ->with('user:id,name,email')
            ->orderByDesc('updated_at')
            ->get()
            ->map(fn ($e) => $this->serializeManualEntry($e));

        return response()->json(['items' => $entries]);
    }

    public function manualStore(Request $request)
    {
        $validated = $this->validateManualPayload($request);
        $sub = $this->resolveSubcategory($validated['group_id'], $validated['subcategory_id']);

        $entry = ManualRankingEntry::create([
            ...$this->manualAttributesFromValidated($validated, $sub, $request),
            'created_by' => $request->user()->id,
        ]);

        $entry->load('user:id,name,email');

        $this->autoReportFromManualEntry($entry, $sub);

        return response()->json($this->serializeManualEntry($entry), 201);
    }

    public function manualUpdate(Request $request, ManualRankingEntry $entry)
    {
        $validated = $request->validate([
            'score' => 'required|numeric',
            'unit' => 'nullable|string|max:32',
            'notes' => 'nullable|string|max:2000',
        ]);

        $sub = $this->resolveSubcategory($entry->group_id, $entry->subcategory_id);
        $unit = $validated['unit'] ?? $sub['unit'] ?? null;

        $entry->update([
            'score' => $validated['score'],
            'unit' => $unit,
            'notes' => $validated['notes'] ?? null,
        ]);

        $entry->load('user:id,name,email');

        if ($entry->group_id === 'jasmani' && $entry->user) {
            app(AutoStudentReportService::class)->fromJasmaniScore(
                $entry->user,
                $sub,
                (float) $entry->score,
                $request->user()->id,
                $entry->notes,
                null,
                $entry->bimble_class_id,
            );
        }

        return response()->json($this->serializeManualEntry($entry));
    }

    public function manualDestroy(ManualRankingEntry $entry)
    {
        $entry->delete();

        return response()->json(['success' => true]);
    }

    private function validateRankingContext(Request $request): array
    {
        return $request->validate([
            'scope' => 'required|in:global,class,cohort',
            'group_id' => 'required|string',
            'subcategory_id' => 'required|string',
            'class_id' => [
                Rule::requiredIf(fn () => $request->input('scope') === 'class'),
                'nullable',
                'integer',
                'exists:bimble_classes,id',
            ],
            'cohort' => [
                Rule::requiredIf(fn () => $request->input('scope') === 'cohort'),
                'nullable',
                'string',
                'max:120',
            ],
        ]);
    }

    private function validateManualPayload(Request $request): array
    {
        $validated = $this->validateRankingContext($request);
        $validated = array_merge($validated, $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'score' => 'required|numeric',
            'unit' => 'nullable|string|max:32',
            'notes' => 'nullable|string|max:2000',
        ]));

        $key = ManualRankingEntry::buildContextKey([
            'scope' => $validated['scope'],
            'group_id' => $validated['group_id'],
            'subcategory_id' => $validated['subcategory_id'],
            'bimble_class_id' => $validated['scope'] === 'class' ? ($validated['class_id'] ?? null) : null,
            'cohort' => $validated['scope'] === 'cohort' ? ($validated['cohort'] ?? null) : null,
            'user_id' => $validated['user_id'],
        ]);

        if (ManualRankingEntry::where('context_key', $key)->exists()) {
            throw ValidationException::withMessages([
                'user_id' => ['Peringkat manual untuk peserta ini sudah ada. Gunakan ubah.'],
            ]);
        }

        return $validated;
    }

    private function manualAttributesFromValidated(array $validated, array $sub, Request $request): array
    {
        $classId = $validated['scope'] === 'class' ? ($validated['class_id'] ?? null) : null;
        $cohort = $validated['scope'] === 'cohort' ? ($validated['cohort'] ?? null) : null;

        $attrs = [
            'scope' => $validated['scope'],
            'group_id' => $validated['group_id'],
            'subcategory_id' => $validated['subcategory_id'],
            'bimble_class_id' => $classId,
            'cohort' => $cohort,
            'user_id' => $validated['user_id'],
            'score' => $validated['score'],
            'unit' => $validated['unit'] ?? $sub['unit'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ];

        $attrs['context_key'] = ManualRankingEntry::buildContextKey($attrs);

        return $attrs;
    }

    private function manualContextWhere(array $validated): array
    {
        $where = [
            'scope' => $validated['scope'],
            'group_id' => $validated['group_id'],
            'subcategory_id' => $validated['subcategory_id'],
        ];

        if ($validated['scope'] === 'class') {
            $where['bimble_class_id'] = $validated['class_id'];
        } else {
            $where['bimble_class_id'] = null;
        }

        if ($validated['scope'] === 'cohort') {
            $where['cohort'] = $validated['cohort'];
        } else {
            $where['cohort'] = null;
        }

        return $where;
    }

    private function resolveSubcategory(string $groupId, string $subId): array
    {
        $group = $this->findGroup($groupId);
        $sub = $group ? $this->findSubcategory($group, $subId) : null;

        if (! $sub) {
            abort(404, 'Subkategori tidak ditemukan.');
        }

        return $sub;
    }

    private function findGroup(string $groupId): ?array
    {
        foreach (config('rankings.groups', []) as $group) {
            if (($group['id'] ?? '') === $groupId) {
                return $group;
            }
        }

        return null;
    }

    private function findSubcategory(array $group, string $subId): ?array
    {
        foreach ($group['subcategories'] ?? [] as $sub) {
            if (($sub['id'] ?? '') === $subId) {
                return $sub;
            }
        }

        return null;
    }

    private function resolveUserIds(string $scope, ?int $classId, ?string $cohort): ?Collection
    {
        if ($scope === 'global') {
            return null;
        }

        if (! Schema::hasTable('bimble_classes')) {
            return collect();
        }

        if ($scope === 'class' && $classId) {
            $class = BimbleClass::with('students:id')->find($classId);
            if (! $class) {
                return collect();
            }

            return $class->students->pluck('id');
        }

        if ($scope === 'cohort' && $cohort) {
            $ids = Schema::hasTable('registration_progress')
                ? RegistrationProgress::query()
                    ->get(['user_id', 'administration_data'])
                    ->filter(function ($row) use ($cohort) {
                        $data = $row->administration_data ?? [];
                        $value = $data['angkatan'] ?? $data['cohort'] ?? $data['batch'] ?? null;

                        return $value && (string) $value === (string) $cohort;
                    })
                    ->pluck('user_id')
                : collect();

            $classUserIds = BimbleClass::query()
                ->where('academic_period', $cohort)
                ->with('students:id')
                ->get()
                ->flatMap(fn ($c) => $c->students->pluck('id'));

            return $ids->merge($classUserIds)->unique()->values();
        }

        return collect();
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function buildManualLeaderboard(array $context, array $sub): array
    {
        if (! Schema::hasTable('manual_ranking_entries')) {
            return [];
        }

        $unit = $sub['unit'] ?? null;
        $sortAsc = ($sub['sort'] ?? 'desc') === 'asc';

        $entries = ManualRankingEntry::query()
            ->where($this->manualContextWhere($context))
            ->with('user:id,name')
            ->get();

        $rows = [];
        foreach ($entries as $entry) {
            if (! $entry->user) {
                continue;
            }
            $value = (float) $entry->score;
            $rows[] = [
                'user_id' => $entry->user_id,
                'name' => $entry->user->name,
                'score' => $value,
                'display' => $this->formatScoreDisplay($value, $entry->unit ?? $unit, null),
                'unit' => $entry->unit ?? $unit,
                'source' => 'manual',
                'manual_id' => $entry->id,
                'notes' => $entry->notes,
            ];
        }

        usort($rows, function ($a, $b) use ($sortAsc) {
            return $sortAsc
                ? $a['score'] <=> $b['score']
                : $b['score'] <=> $a['score'];
        });

        return $rows;
    }

    /**
     * @param  list<array<string, mixed>>  $auto
     * @param  list<array<string, mixed>>  $manual
     * @return list<array<string, mixed>>
     */
    private function mergeLeaderboards(array $auto, array $manual, array $sub): array
    {
        $sortAsc = ($sub['sort'] ?? 'desc') === 'asc';
        $byUser = [];

        foreach ($auto as $row) {
            $row['source'] = $row['source'] ?? 'auto';
            $byUser[$row['user_id']] = $row;
        }

        foreach ($manual as $row) {
            $byUser[$row['user_id']] = $row;
        }

        $rows = array_values($byUser);
        usort($rows, function ($a, $b) use ($sortAsc) {
            return $sortAsc
                ? $a['score'] <=> $b['score']
                : $b['score'] <=> $a['score'];
        });

        return $this->assignRanks($rows);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function buildPhysicalLeaderboard(array $sub, ?Collection $userIds): array
    {
        if (! Schema::hasTable('registration_progress')) {
            return [];
        }

        $subId = $sub['id'];
        $sortAsc = ($sub['sort'] ?? 'desc') === 'asc';
        $unit = $sub['unit'] ?? null;

        $query = RegistrationProgress::query()
            ->whereNotNull('physical_data')
            ->with('user:id,name');

        if ($userIds !== null) {
            if ($userIds->isEmpty()) {
                return [];
            }
            $query->whereIn('user_id', $userIds);
        }

        $rows = [];
        foreach ($query->get() as $progress) {
            $raw = $progress->physical_data[$subId] ?? null;
            $value = $this->extractNumericScore($raw);
            if ($value === null || ! $progress->user) {
                continue;
            }

            $rows[] = [
                'user_id' => $progress->user_id,
                'name' => $progress->user->name,
                'score' => $value,
                'display' => $this->formatPhysicalDisplay($value, $unit),
                'unit' => $unit,
                'source' => 'auto',
            ];
        }

        usort($rows, function ($a, $b) use ($sortAsc) {
            return $sortAsc
                ? $a['score'] <=> $b['score']
                : $b['score'] <=> $a['score'];
        });

        return $rows;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function buildAcademicLeaderboard(array $sub, ?Collection $userIds): array
    {
        $categories = $sub['test_categories'] ?? [];
        if ($categories === [] || ! Schema::hasTable('test_definitions') || ! Schema::hasTable('test_submissions')) {
            return [];
        }

        $testIds = TestDefinition::query()
            ->whereIn('category', $categories)
            ->pluck('id');

        if ($testIds->isEmpty()) {
            return [];
        }

        $query = TestSubmission::query()
            ->whereIn('test_definition_id', $testIds)
            ->whereNotNull('score')
            ->with(['user:id,name', 'testDefinition:id,category,question_ids']);

        if ($userIds !== null) {
            if ($userIds->isEmpty()) {
                return [];
            }
            $query->whereIn('user_id', $userIds);
        }

        $bestByUser = [];
        foreach ($query->get() as $submission) {
            if (! $submission->user) {
                continue;
            }

            $total = count($submission->testDefinition?->question_ids ?? []);
            if ($total < 1) {
                continue;
            }

            $pct = round(($submission->score / $total) * 100, 1);
            $uid = $submission->user_id;

            if (! isset($bestByUser[$uid]) || $pct > $bestByUser[$uid]['score']) {
                $bestByUser[$uid] = [
                    'user_id' => $uid,
                    'name' => $submission->user->name,
                    'score' => $pct,
                    'display' => "{$pct}%",
                    'unit' => '%',
                    'source' => 'auto',
                ];
            }
        }

        $rows = array_values($bestByUser);
        usort($rows, fn ($a, $b) => $b['score'] <=> $a['score']);

        return $rows;
    }

    private function extractNumericScore(mixed $raw): ?float
    {
        if (is_numeric($raw)) {
            return (float) $raw;
        }

        if (is_array($raw)) {
            foreach (['value', 'score', 'result', 'nilai'] as $key) {
                if (isset($raw[$key]) && is_numeric($raw[$key])) {
                    return (float) $raw[$key];
                }
            }
        }

        return null;
    }

    private function formatPhysicalDisplay(float $value, ?string $unit): string
    {
        return $this->formatScoreDisplay($value, $unit, null);
    }

    private function formatScoreDisplay(float $value, ?string $unit, ?string $suffix): string
    {
        $formatted = fmod($value, 1.0) === 0.0 ? (string) (int) $value : (string) round($value, 2);

        if ($suffix === '%') {
            return "{$formatted}%";
        }

        return $unit ? "{$formatted} {$unit}" : $formatted;
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     * @return list<array<string, mixed>>
     */
    private function assignRanks(array $rows): array
    {
        $ranked = [];
        $rank = 0;
        $prevScore = null;
        $index = 0;

        foreach ($rows as $row) {
            $index++;
            if ($prevScore === null || $row['score'] !== $prevScore) {
                $rank = $index;
                $prevScore = $row['score'];
            }

            $ranked[] = array_merge($row, ['rank' => $rank]);
        }

        return $ranked;
    }

    private function serializeManualEntry(ManualRankingEntry $entry): array
    {
        return [
            'id' => $entry->id,
            'scope' => $entry->scope,
            'group_id' => $entry->group_id,
            'subcategory_id' => $entry->subcategory_id,
            'bimble_class_id' => $entry->bimble_class_id,
            'cohort' => $entry->cohort,
            'user_id' => $entry->user_id,
            'user' => $entry->user ? [
                'id' => $entry->user->id,
                'name' => $entry->user->name,
                'email' => $entry->user->email,
            ] : null,
            'score' => $entry->score,
            'unit' => $entry->unit,
            'notes' => $entry->notes,
            'updated_at' => $entry->updated_at?->toIso8601String(),
        ];
    }

    private function autoReportFromManualEntry(ManualRankingEntry $entry, array $sub): void
    {
        if ($entry->group_id !== 'jasmani' || ! $entry->user) {
            return;
        }

        app(AutoStudentReportService::class)->fromJasmaniScore(
            $entry->user,
            $sub,
            (float) $entry->score,
            $entry->created_by,
            $entry->notes,
            $entry->id,
            $entry->bimble_class_id,
        );
    }
}
