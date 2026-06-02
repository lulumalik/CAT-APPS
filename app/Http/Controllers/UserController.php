<?php

namespace App\Http\Controllers;

use App\Models\RegistrationProgress;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function searchableStudents(Request $request)
    {
        $q = User::query()->where('role', 'user');
        if ($request->filled('search')) {
            $s = $request->string('search')->toString();
            $q->where(function ($x) use ($s) {
                $x->where('name', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('username', 'like', "%{$s}%");
            });
        }

        return response()->json(
            $q->orderBy('name')
                ->limit(30)
                ->get(['id', 'name', 'username', 'email', 'program_category', 'in_quarantine'])
        );
    }

    public function index(Request $request)
    {
        $query = User::query();

        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(10);

        return response()->json($users);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|min:3|max:32|regex:/^[a-zA-Z0-9_]+$/|unique:users,username',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,user,mentor',
            'program_category' => 'nullable|in:'.implode(',', User::programCategories()),
            'in_quarantine' => 'nullable|boolean',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'username' => User::normalizeUsername($validated['username']),
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'program_category' => User::normalizeProgramCategory($validated['program_category'] ?? User::PROGRAM_REGULAR),
            'in_quarantine' => User::supportsQuarantine($validated['program_category'] ?? User::PROGRAM_REGULAR)
                ? (bool) ($validated['in_quarantine'] ?? false)
                : false,
        ]);

        $this->ensureRegistrationProgress($user);

        return response()->json($user, 201);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => ['required', 'string', 'min:3', 'max:32', 'regex:/^[a-zA-Z0-9_]+$/', Rule::unique('users', 'username')->ignore($user->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => 'required|in:admin,user,mentor',
            'password' => 'nullable|string|min:8',
            'program_category' => 'nullable|in:'.implode(',', User::programCategories()),
            'in_quarantine' => 'nullable|boolean',
        ]);

        $user->name = $validated['name'];
        $user->username = User::normalizeUsername($validated['username']);
        $user->email = $validated['email'];
        $user->role = $validated['role'];

        if (array_key_exists('program_category', $validated) && $validated['program_category'] !== null) {
            $user->program_category = User::normalizeProgramCategory($validated['program_category']);
        }

        if (array_key_exists('in_quarantine', $validated) && $validated['in_quarantine'] !== null) {
            $user->in_quarantine = User::supportsQuarantine($user->program_category)
                ? (bool) $validated['in_quarantine']
                : false;
        }

        if (! User::supportsQuarantine($user->program_category)) {
            $user->in_quarantine = false;
        }

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();
        $this->ensureRegistrationProgress($user);

        return response()->json($user);
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'Cannot delete yourself.'], 403);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully.']);
    }

    public function import(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx',
            'default_password' => 'nullable|string|min:8|max:72',
        ]);

        $file = $validated['file'];
        $extension = strtolower($file->getClientOriginalExtension());
        $rows = $extension === 'xlsx'
            ? $this->parseXlsxRows($file->getRealPath())
            : $this->parseCsvRows($file->getRealPath());

        if (empty($rows)) {
            return response()->json([
                'message' => 'File tidak berisi data user yang valid.',
            ], 422);
        }

        $defaultPassword = $validated['default_password'] ?? 'password123';
        $created = 0;
        $updated = 0;
        $skipped = [];

        foreach ($rows as $idx => $row) {
            $name = trim((string) ($row['name'] ?? ''));
            $email = trim(strtolower((string) ($row['email'] ?? '')));
            $role = trim((string) ($row['role'] ?? 'user'));
            $programCategory = User::normalizeProgramCategory(trim((string) ($row['program_category'] ?? User::PROGRAM_REGULAR)));
            $inQuarantine = filter_var($row['in_quarantine'] ?? false, FILTER_VALIDATE_BOOLEAN);
            $passwordRaw = (string) ($row['password'] ?? '');

            if ($name === '' || $email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $skipped[] = ['row' => $idx + 2, 'reason' => 'name/email tidak valid'];
                continue;
            }

            if (! in_array($role, ['admin', 'mentor', 'user'], true)) {
                $skipped[] = ['row' => $idx + 2, 'reason' => 'role tidak dikenali'];
                continue;
            }

            $payload = [
                'name' => $name,
                'username' => User::generateUniqueUsername($email, $name),
                'role' => $role,
                'program_category' => $programCategory,
                'in_quarantine' => User::supportsQuarantine($programCategory) ? $inQuarantine : false,
            ];

            $existing = User::where('email', $email)->first();
            if (! $existing) {
                $payload['email'] = $email;
                $payload['password'] = Hash::make($passwordRaw !== '' ? $passwordRaw : $defaultPassword);
                $existing = User::create($payload);
                $created++;
            } else {
                if ($passwordRaw !== '') {
                    $payload['password'] = Hash::make($passwordRaw);
                }
                $existing->update($payload);
                $updated++;
            }

            $this->ensureRegistrationProgress($existing);
        }

        return response()->json([
            'message' => 'Import user selesai.',
            'stats' => [
                'created' => $created,
                'updated' => $updated,
                'skipped' => count($skipped),
            ],
            'skipped_rows' => $skipped,
            'default_password' => $defaultPassword,
        ]);
    }

    private function ensureRegistrationProgress(User $user): void
    {
        if ($user->role !== 'user' || ! Schema::hasTable('registration_progress')) {
            return;
        }

        RegistrationProgress::firstOrCreate(
            ['user_id' => $user->id],
            [
                'current_step' => 'administration',
                'administration_status' => 'not_started',
                'health_status' => 'not_started',
                'psychology_status' => 'not_started',
                'fully_completed' => false,
            ]
        );
    }

    private function parseCsvRows(string $path): array
    {
        $handle = fopen($path, 'rb');
        if (! $handle) {
            return [];
        }

        $header = fgetcsv($handle) ?: [];
        $header = array_map(fn ($h) => Str::snake(trim((string) $h)), $header);
        $rows = [];

        while (($line = fgetcsv($handle)) !== false) {
            if (count(array_filter($line, fn ($v) => trim((string) $v) !== '')) === 0) {
                continue;
            }
            $rows[] = array_combine($header, array_pad($line, count($header), null));
        }

        fclose($handle);
        return $rows;
    }

    private function parseXlsxRows(string $path): array
    {
        $zip = new \ZipArchive();
        if ($zip->open($path) !== true) {
            return [];
        }

        $sharedStrings = [];
        $sharedStringsXml = $zip->getFromName('xl/sharedStrings.xml');
        if ($sharedStringsXml) {
            $ss = simplexml_load_string($sharedStringsXml);
            if ($ss && isset($ss->si)) {
                foreach ($ss->si as $item) {
                    $sharedStrings[] = (string) ($item->t ?? '');
                }
            }
        }

        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();
        if (! $sheetXml) {
            return [];
        }

        $sheet = simplexml_load_string($sheetXml);
        if (! $sheet || ! isset($sheet->sheetData->row)) {
            return [];
        }

        $rawRows = [];
        foreach ($sheet->sheetData->row as $row) {
            $values = [];
            foreach ($row->c as $cell) {
                $ref = (string) $cell['r'];
                $col = preg_replace('/\d+/', '', $ref);
                $index = $this->columnToIndex($col);
                $type = (string) $cell['t'];
                $val = (string) ($cell->v ?? '');
                if ($type === 's') {
                    $val = $sharedStrings[(int) $val] ?? '';
                }
                $values[$index] = $val;
            }
            if (! empty($values)) {
                ksort($values);
                $rawRows[] = $values;
            }
        }

        if (empty($rawRows)) {
            return [];
        }

        $headerRow = array_shift($rawRows);
        $maxCol = max(array_keys($headerRow));
        $headers = [];
        for ($i = 0; $i <= $maxCol; $i++) {
            $headers[$i] = Str::snake(trim((string) ($headerRow[$i] ?? '')));
        }

        $rows = [];
        foreach ($rawRows as $r) {
            $item = [];
            for ($i = 0; $i <= $maxCol; $i++) {
                $key = $headers[$i] ?? '';
                if ($key === '') {
                    continue;
                }
                $item[$key] = $r[$i] ?? null;
            }
            if (count(array_filter($item, fn ($v) => trim((string) $v) !== '')) > 0) {
                $rows[] = $item;
            }
        }

        return $rows;
    }

    private function columnToIndex(string $column): int
    {
        $column = strtoupper($column);
        $index = 0;
        $len = strlen($column);
        for ($i = 0; $i < $len; $i++) {
            $index = $index * 26 + (ord($column[$i]) - 64);
        }
        return max(0, $index - 1);
    }
}
