<?php

namespace Tests\Feature;

use App\Models\Question;
use App\Models\TestDefinition;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TestDefinitionQuestionAssignmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_assign_questions_after_creating_test_without_questions(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $question = Question::create([
            'question' => 'Contoh soal?',
            'category' => 'Math',
            'difficulty' => 'Easy',
            'type' => 'multiple_choice',
            'options' => ['A', 'B', 'C', 'D'],
            'correct' => 'A',
            'created_by' => $admin->id,
        ]);

        $start = now()->addDay()->setSeconds(0);
        $end = (clone $start)->addHour()->setSeconds(0);

        $createPayload = [
            'name' => 'Tes tanpa soal',
            'description' => 'Tes dibuat tanpa mengisi soal',
            'category' => 'Math',
            'duration' => 60,
            'schedule_at' => $start->toISOString(),
            'start_time' => $start->toISOString(),
            'end_time' => $end->toISOString(),
        ];

        $create = $this->actingAs($admin)->postJson('/api/tests', $createPayload);
        $create->assertCreated();
        $createdId = $create->json('id');

        $create->assertJsonPath('question_ids', []);
        $create->assertJsonPath('is_active', false);

        $updatePayload = array_merge($createPayload, [
            'question_ids' => [$question->id],
            'is_active' => true,
        ]);

        $update = $this->actingAs($admin)->putJson("/api/tests/{$createdId}", $updatePayload);
        $update->assertOk();
        $update->assertJsonPath('question_ids.0', $question->id);
        $update->assertJsonPath('is_active', true);

        $db = TestDefinition::findOrFail($createdId);
        $this->assertSame([$question->id], $db->question_ids);
        $this->assertTrue((bool) $db->is_active);
    }
}

