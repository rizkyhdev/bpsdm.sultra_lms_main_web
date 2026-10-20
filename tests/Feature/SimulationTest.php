<?php

namespace Tests\Feature;

use App\Models\SimulationAttempt;
use App\Models\SimulationQuestion;
use App\Models\SimulationToken;
use App\Models\User;
use App\Services\SimulationService;
use Database\Seeders\SimulationQuestionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SimulationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin', 'is_validated' => true]);
    }

    private function student(): User
    {
        return User::factory()->create([
            'role' => 'student',
            'is_validated' => true,
            'nip' => fake()->unique()->numerify('19##############'),
            'unit_kerja' => 'BPSDM Provinsi Sulawesi Tenggara',
        ]);
    }

    private function startAttempt(User $student, int $bankSize = 3): SimulationAttempt
    {
        SimulationQuestion::factory()->count($bankSize)->create();
        $token = app(SimulationService::class)->generateToken('Sesi Uji');

        $this->actingAs($student)
            ->post(route('student.simulasi.start'), ['token' => strtolower($token->kode)])
            ->assertRedirect();

        return $student->simulationAttempts()->latest('id')->firstOrFail();
    }

    private function answer(SimulationAttempt $attempt, SimulationQuestion $question, string $jawaban)
    {
        return $this->postJson(route('student.simulasi.answer', $attempt), [
            'question_id' => $question->id,
            'jawaban' => $jawaban,
        ]);
    }

    private function wrongOption(SimulationQuestion $question): string
    {
        return collect(SimulationQuestion::OPTIONS)->first(fn ($option) => $option !== $question->kunci_jawaban);
    }

    public function test_admin_can_manage_the_question_bank(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.simulasi.soal.index'))
            ->assertOk()
            ->assertSee('Belum ada soal di bank soal.');

        $data = [
            'pertanyaan' => 'Apa kepanjangan PBJP?',
            'pilihan_a' => 'Pengelolaan Belanja Jasa Pemerintah',
            'pilihan_b' => 'Pengadaan Barang/Jasa Pemerintah',
            'pilihan_c' => 'Peraturan Barang/Jasa Pemerintah',
            'pilihan_d' => 'Pelaksanaan Belanja Jasa Pemerintah',
            'kunci_jawaban' => 'b',
        ];

        $this->post(route('admin.simulasi.soal.store'), $data)->assertRedirect(route('admin.simulasi.soal.index'));
        $question = SimulationQuestion::sole();
        $this->assertSame('B', $question->kunci_jawaban);

        $this->get(route('admin.simulasi.soal.index', ['search' => 'KEPANJANGAN']))->assertSee('Apa kepanjangan PBJP?');
        $this->get(route('admin.simulasi.soal.index', ['search' => 'tidak-ada']))->assertDontSee('Apa kepanjangan PBJP?');
        $this->get(route('admin.simulasi.soal.edit', $question))->assertOk()->assertSee('Apa kepanjangan PBJP?');

        $this->put(route('admin.simulasi.soal.update', $question), ['kunci_jawaban' => 'C'] + $data)
            ->assertRedirect(route('admin.simulasi.soal.index'));
        $this->assertSame('C', $question->fresh()->kunci_jawaban);

        $this->delete(route('admin.simulasi.soal.destroy', $question))->assertSessionHas('success');
        $this->assertModelMissing($question);
    }

    public function test_question_requires_all_fields_and_a_valid_key(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.simulasi.soal.store'), ['kunci_jawaban' => 'E'])
            ->assertSessionHasErrors(['pertanyaan', 'pilihan_a', 'pilihan_b', 'pilihan_c', 'pilihan_d', 'kunci_jawaban']);

        $this->assertSame(0, SimulationQuestion::count());
    }

    public function test_question_used_in_a_simulation_cannot_be_deleted(): void
    {
        $attempt = $this->startAttempt($this->student(), 2);
        $question = SimulationQuestion::findOrFail($attempt->question_ids[0]);

        // Belum dijawab, tetapi menjadi bagian dari simulasi yang sedang berjalan.
        $this->actingAs($this->admin())
            ->delete(route('admin.simulasi.soal.destroy', $question))
            ->assertSessionHas('error');

        $this->assertModelExists($question);
    }

    public function test_generating_a_token_deactivates_the_previous_one(): void
    {
        $this->actingAs($this->admin());
        $this->post(route('admin.simulasi.token.store'), ['sesi' => 'Sesi 1'])->assertRedirect(route('admin.simulasi.index'));
        $this->post(route('admin.simulasi.token.store'), ['sesi' => 'Sesi 2']);

        $active = SimulationToken::active()->sole();
        $this->assertSame('Sesi 2', $active->sesi);
        $this->assertMatchesRegularExpression('/^[0-9A-F]{6}$/', $active->kode);
        $this->assertSame(2, SimulationToken::count());

        $this->get(route('admin.simulasi.index'))->assertOk()->assertSee($active->kode);
    }

    public function test_live_score_lists_participants_of_the_active_session(): void
    {
        $student = $this->student();
        $attempt = $this->startAttempt($student, 2);
        $question = SimulationQuestion::findOrFail($attempt->question_ids[0]);
        $this->answer($attempt, $question, $question->kunci_jawaban)->assertOk();

        $this->actingAs($this->admin())
            ->getJson(route('admin.simulasi.live'))
            ->assertOk()
            ->assertJsonPath('sesi', 'Sesi Uji')
            ->assertJsonPath('peserta.0.nama', $student->name)
            ->assertJsonPath('peserta.0.nip', $student->nip)
            ->assertJsonPath('peserta.0.terjawab', 1)
            ->assertJsonPath('peserta.0.benar', 1)
            ->assertJsonPath('peserta.0.status', 'Sedang Mengerjakan');

        $this->get(route('admin.simulasi.index', ['status' => 'berlangsung']))->assertOk()->assertSee($student->name);
        $this->get(route('admin.simulasi.index', ['status' => 'selesai']))->assertOk()->assertDontSee($student->name);
    }

    public function test_students_cannot_open_admin_pages(): void
    {
        $this->actingAs($this->student());

        $this->get(route('admin.simulasi.index'))->assertForbidden();
        $this->get(route('admin.simulasi.soal.index'))->assertForbidden();
        $this->post(route('admin.simulasi.token.store'), ['sesi' => 'X'])->assertForbidden();
    }

    public function test_admins_cannot_open_student_simulation_pages(): void
    {
        $this->actingAs($this->admin())->get(route('student.simulasi.index'))->assertForbidden();
    }

    public function test_invalid_or_inactive_token_is_rejected(): void
    {
        SimulationQuestion::factory()->create();
        $service = app(SimulationService::class);
        $old = $service->generateToken('Sesi Lama');
        $service->generateToken('Sesi Baru');

        $this->actingAs($this->student());
        $this->post(route('student.simulasi.start'), ['token' => 'XXXXXX'])->assertSessionHasErrors('token');
        $this->post(route('student.simulasi.start'), ['token' => $old->kode])->assertSessionHasErrors('token');

        $this->assertSame(0, SimulationAttempt::count());
    }

    public function test_student_takes_a_simulation_and_is_scored(): void
    {
        config(['simulasi.jumlah_soal' => 3]);
        $student = $this->student();

        $this->actingAs($student)->get(route('student.simulasi.index'))->assertOk()->assertSee($student->nip);

        $attempt = $this->startAttempt($student, 5);
        $this->assertSame(3, $attempt->jumlah_soal);
        $this->assertCount(3, array_unique($attempt->question_ids));

        $this->get(route('student.simulasi.exam', $attempt))->assertOk()->assertSee('Soal 1 dari 3');
        $this->get(route('student.simulasi.exam', [$attempt, 3]))->assertOk()->assertSee('Soal 3 dari 3');
        $this->get(route('student.simulasi.exam', [$attempt, 9]))->assertRedirect(route('student.simulasi.exam', $attempt));

        [$first, $second] = SimulationQuestion::findMany(array_slice($attempt->question_ids, 0, 2))->all();
        $this->answer($attempt, $first, $first->kunci_jawaban)->assertOk()->assertJson(['terjawab' => 1]);
        $this->answer($attempt, $second, $second->kunci_jawaban)->assertJson(['terjawab' => 2]);
        // Mengganti jawaban menimpa jawaban sebelumnya.
        $this->answer($attempt, $second, $this->wrongOption($second))->assertJson(['terjawab' => 2]);

        $this->post(route('student.simulasi.finish', $attempt))->assertRedirect(route('student.simulasi.result', $attempt));

        $attempt->refresh();
        $this->assertSame(1, $attempt->skor);
        $this->assertNotNull($attempt->completed_at);

        $this->get(route('student.simulasi.result', $attempt))->assertOk()->assertSee('Hasil Simulasi');
        $this->get(route('student.simulasi.index'))->assertSee('Sesi Uji');

        // Setelah selesai, jawaban tidak dapat diubah lagi.
        $this->answer($attempt, $first, $this->wrongOption($first))->assertStatus(409);
        $this->get(route('student.simulasi.exam', $attempt))->assertRedirect(route('student.simulasi.result', $attempt));
        $this->assertSame(1, $attempt->fresh()->skor);
    }

    public function test_answer_must_belong_to_the_attempt_and_be_a_valid_option(): void
    {
        $attempt = $this->startAttempt($this->student(), 1);
        $question = SimulationQuestion::findOrFail($attempt->question_ids[0]);
        $other = SimulationQuestion::factory()->create();

        $this->answer($attempt, $other, 'A')->assertUnprocessable()->assertJsonValidationErrors('question_id');
        $this->answer($attempt, $question, 'E')->assertUnprocessable()->assertJsonValidationErrors('jawaban');
    }

    public function test_student_cannot_access_another_students_attempt(): void
    {
        $attempt = $this->startAttempt($this->student(), 1);
        $question = SimulationQuestion::findOrFail($attempt->question_ids[0]);

        $this->actingAs($this->student());
        $this->get(route('student.simulasi.exam', $attempt))->assertForbidden();
        $this->answer($attempt, $question, 'A')->assertForbidden();
        $this->post(route('student.simulasi.finish', $attempt))->assertForbidden();
        $this->get(route('student.simulasi.result', $attempt))->assertForbidden();
    }

    public function test_starting_again_resumes_the_ongoing_attempt(): void
    {
        $student = $this->student();
        $attempt = $this->startAttempt($student, 2);

        $this->post(route('student.simulasi.start'), ['token' => SimulationToken::active()->sole()->kode])
            ->assertRedirect(route('student.simulasi.exam', $attempt));

        $this->assertSame(1, SimulationAttempt::count());
    }

    public function test_attempt_is_finished_automatically_when_time_runs_out(): void
    {
        $attempt = $this->startAttempt($this->student(), 2);
        $question = SimulationQuestion::findOrFail($attempt->question_ids[0]);
        $this->answer($attempt, $question, $question->kunci_jawaban)->assertOk();

        $this->travel(config('simulasi.durasi_menit') + 1)->minutes();

        $this->answer($attempt, $question, $this->wrongOption($question))->assertStatus(409);
        $this->get(route('student.simulasi.exam', $attempt))->assertRedirect(route('student.simulasi.result', $attempt));

        $attempt->refresh();
        $this->assertSame(1, $attempt->skor);
        $this->assertTrue($attempt->completed_at->equalTo($attempt->ends_at));
    }

    public function test_landing_page_presents_the_simulation(): void
    {
        SimulationQuestion::factory()->count(4)->create();

        $this->get('/')
            ->assertOk()
            ->assertSee('Simulasi Uji Kompetensi Berbasis CAT')
            ->assertSee('4 soal pilihan ganda')
            ->assertSee(route('login'));
    }

    public function test_seeder_imports_the_question_bank_once(): void
    {
        $this->seed(SimulationQuestionSeeder::class);
        $this->assertSame(137, SimulationQuestion::count());

        $this->seed(SimulationQuestionSeeder::class);
        $this->assertSame(137, SimulationQuestion::count());
        $this->assertSame(0, SimulationQuestion::whereNotIn('kunci_jawaban', SimulationQuestion::OPTIONS)->count());
    }
}
