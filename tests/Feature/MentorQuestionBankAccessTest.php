<?php

namespace Tests\Feature;

use App\Models\Question;
use App\Models\TestDefinition;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MentorQuestionBankAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_mentor_can_list_all_questions_but_only_own_tests(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $mentor = User::factory()->create(['role' => 'mentor']);
        $otherMentor = User::factory()->create(['role' => 'mentor']);

        $adminQuestion = Question::create([
            'question' => 'Soal admin',
            'category' => 'Math',
            'difficulty' => 'Easy',
            'type' => 'multiple_choice',
            'options' => ['A', 'B', 'C', 'D'],
            'correct' => 'A',
            'created_by' => $admin->id,
        ]);

        $mentorQuestion = Question::create([
            'question' => 'Soal mentor',
            'category' => 'Math',
            'difficulty' => 'Medium',
            'type' => 'multiple_choice',
            'options' => ['A', 'B', 'C', 'D'],
            'correct' => 'B',
            'created_by' => $mentor->id,
        ]);

        $otherQuestion = Question::create([
            'question' => 'Soal mentor lain',
            'category' => 'Science',
            'difficulty' => 'Hard',
            'type' => 'multiple_choice',
            'options' => ['A', 'B', 'C', 'D'],
            'correct' => 'C',
            'created_by' => $otherMentor->id,
        ]);

        $start = now()->addDay()->setSeconds(0);
        $end = (clone $start)->addHour()->setSeconds(0);

        $ownTest = TestDefinition::create([
            'name' => 'Tes mentor',
            'category' => 'Math',
            'duration' => 60,
            'schedule_at' => $start,
            'start_time' => $start,
            'end_time' => $end,
            'question_ids' => [$mentorQuestion->id],
            'created_by' => $mentor->id,
        ]);

        TestDefinition::create([
            'name' => 'Tes mentor lain',
            'category' => 'Science',
            'duration' => 60,
            'schedule_at' => $start,
            'start_time' => $start,
            'end_time' => $end,
            'question_ids' => [$otherQuestion->id],
            'created_by' => $otherMentor->id,
        ]);

        $questions = $this->actingAs($mentor)->getJson('/api/questions');
        $questions->assertOk();
        $questionIds = collect($questions->json('items'))->pluck('id')->all();
        $this->assertEqualsCanonicalizing(
            [$adminQuestion->id, $mentorQuestion->id, $otherQuestion->id],
            $questionIds,
        );

        $tests = $this->actingAs($mentor)->getJson('/api/tests');
        $tests->assertOk();
        $this->assertSame([$ownTest->id], collect($tests->json())->pluck('id')->all());
    }

    public function test_mentor_can_assign_any_question_to_own_test(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $mentor = User::factory()->create(['role' => 'mentor']);

        $adminQuestion = Question::create([
            'question' => 'Soal admin',
            'category' => 'Math',
            'difficulty' => 'Easy',
            'type' => 'multiple_choice',
            'options' => ['A', 'B', 'C', 'D'],
            'correct' => 'A',
            'created_by' => $admin->id,
        ]);

        $start = now()->addDay()->setSeconds(0);
        $end = (clone $start)->addHour()->setSeconds(0);

        $create = $this->actingAs($mentor)->postJson('/api/tests', [
            'name' => 'Tes mentor',
            'category' => 'Math',
            'duration' => 60,
            'schedule_at' => $start->toISOString(),
            'start_time' => $start->toISOString(),
            'end_time' => $end->toISOString(),
            'question_ids' => [$adminQuestion->id],
            'is_active' => true,
        ]);

        $create->assertCreated();
        $create->assertJsonPath('question_ids.0', $adminQuestion->id);
    }

    public function test_mentor_cannot_edit_or_delete_other_peoples_questions(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $mentor = User::factory()->create(['role' => 'mentor']);

        $adminQuestion = Question::create([
            'question' => 'Soal admin',
            'category' => 'Math',
            'difficulty' => 'Easy',
            'type' => 'multiple_choice',
            'options' => ['A', 'B', 'C', 'D'],
            'correct' => 'A',
            'created_by' => $admin->id,
        ]);

        $this->actingAs($mentor)->putJson("/api/questions/{$adminQuestion->id}", [
            'question' => 'Diubah mentor',
            'category' => 'Math',
            'difficulty' => 'Easy',
            'type' => 'multiple_choice',
            'options' => ['A', 'B', 'C', 'D'],
            'correct' => 'A',
        ])->assertForbidden();

        $this->actingAs($mentor)->deleteJson("/api/questions/{$adminQuestion->id}")
            ->assertForbidden();
    }

    public function test_mentor_questions_are_listed_before_global_questions(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $mentor = User::factory()->create(['role' => 'mentor']);

        $adminQuestion = Question::create([
            'question' => 'Soal admin',
            'category' => 'Math',
            'difficulty' => 'Easy',
            'type' => 'multiple_choice',
            'options' => ['A', 'B', 'C', 'D'],
            'correct' => 'A',
            'created_by' => $admin->id,
        ]);

        $mentorQuestion = Question::create([
            'question' => 'Soal mentor',
            'category' => 'Math',
            'difficulty' => 'Medium',
            'type' => 'multiple_choice',
            'options' => ['A', 'B', 'C', 'D'],
            'correct' => 'B',
            'created_by' => $mentor->id,
        ]);

        $response = $this->actingAs($mentor)->getJson('/api/questions');
        $response->assertOk();

        $ids = collect($response->json('items'))->pluck('id')->all();
        $this->assertSame([$mentorQuestion->id, $adminQuestion->id], $ids);
    }

    public function test_mentor_can_edit_own_question(): void
    {
        $mentor = User::factory()->create(['role' => 'mentor']);

        $mentorQuestion = Question::create([
            'question' => 'Soal mentor',
            'category' => 'Math',
            'difficulty' => 'Medium',
            'type' => 'multiple_choice',
            'options' => ['A', 'B', 'C', 'D'],
            'correct' => 'B',
            'created_by' => $mentor->id,
        ]);

        $this->actingAs($mentor)->putJson("/api/questions/{$mentorQuestion->id}", [
            'question' => 'Soal mentor diperbarui',
            'category' => 'Math',
            'difficulty' => 'Medium',
            'type' => 'multiple_choice',
            'options' => ['A', 'B', 'C', 'D'],
            'correct' => 'B',
        ])->assertOk()->assertJsonPath('question', 'Soal mentor diperbarui');
    }

    public function test_admin_can_delete_all_questions(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        Question::create([
            'question' => 'Soal 1',
            'category' => 'Math',
            'difficulty' => 'Easy',
            'type' => 'multiple_choice',
            'options' => ['A', 'B', 'C', 'D'],
            'correct' => 'A',
            'created_by' => $admin->id,
        ]);

        Question::create([
            'question' => 'Soal 2',
            'category' => 'Math',
            'difficulty' => 'Easy',
            'type' => 'multiple_choice',
            'options' => ['A', 'B', 'C', 'D'],
            'correct' => 'A',
            'created_by' => $admin->id,
        ]);

        $response = $this->actingAs($admin)->deleteJson('/api/questions/all');
        $response->assertOk()->assertJsonPath('deleted_count', 2);
        $this->assertDatabaseCount('questions', 0);
    }

    public function test_mentor_can_only_delete_all_own_questions(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $mentor = User::factory()->create(['role' => 'mentor']);

        $adminQuestion = Question::create([
            'question' => 'Soal Admin',
            'category' => 'Math',
            'difficulty' => 'Easy',
            'type' => 'multiple_choice',
            'options' => ['A', 'B', 'C', 'D'],
            'correct' => 'A',
            'created_by' => $admin->id,
        ]);

        $mentorQuestion = Question::create([
            'question' => 'Soal Mentor',
            'category' => 'Math',
            'difficulty' => 'Easy',
            'type' => 'multiple_choice',
            'options' => ['A', 'B', 'C', 'D'],
            'correct' => 'A',
            'created_by' => $mentor->id,
        ]);

        $response = $this->actingAs($mentor)->deleteJson('/api/questions/all');
        $response->assertOk()->assertJsonPath('deleted_count', 1);

        $this->assertDatabaseHas('questions', ['id' => $adminQuestion->id]);
        $this->assertDatabaseMissing('questions', ['id' => $mentorQuestion->id]);
    }
}
