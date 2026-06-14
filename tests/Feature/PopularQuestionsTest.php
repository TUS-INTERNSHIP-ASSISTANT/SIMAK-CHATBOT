<?php

namespace Tests\Feature;

use App\Models\ChatLog;
use App\Models\User;
use App\Http\Controllers\KnowledgeBaseController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PopularQuestionsTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();

        $this->user = User::factory()->create([
            'role' => 'staff',
        ]);
    }

    /**
     * Test normalizer logic.
     */
    public function test_normalization_logic()
    {
        $controller = new KnowledgeBaseController();

        $v1 = $controller->normalizeQuestion('Apa syarat Kerja Praktik?');
        $v2 = $controller->normalizeQuestion('syarat Kerja Praktik?');
        $v3 = $controller->normalizeQuestion('Apa saja syarat Kerja Praktik?');
        $v4 = $controller->normalizeQuestion('syarat Kerja Praktik apa?');

        $expected = 'kerja praktik syarat';

        $this->assertEquals($expected, $v1);
        $this->assertEquals($expected, $v2);
        $this->assertEquals($expected, $v3);
        $this->assertEquals($expected, $v4);
    }

    /**
     * Test chatbot query logs query and stores normalized key.
     */
    public function test_chatbot_query_logs_interaction_to_database()
    {
        $response = $this->postJson(route('chatbot.query'), [
            'query' => 'Apa syarat Kerja Praktik?',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('chat_logs', [
            'message' => 'Apa syarat Kerja Praktik?',
            'normalized_message' => 'kerja praktik syarat',
        ]);
    }

    /**
     * Test home dashboard loads dynamic popular questions.
     */
    public function test_dashboard_home_shows_popular_questions()
    {
        // Seed some varying queries
        ChatLog::create([
            'message' => 'Apa syarat Kerja Praktik?',
            'response' => 'Jawaban',
            'normalized_message' => 'kerja praktik syarat',
        ]);
        ChatLog::create([
            'message' => 'syarat Kerja Praktik?',
            'response' => 'Jawaban',
            'normalized_message' => 'kerja praktik syarat',
        ]);
        ChatLog::create([
            'message' => 'sks minimal magang?',
            'response' => 'Jawaban',
            'normalized_message' => 'magang minimal sks',
        ]);

        $response = $this->actingAs($this->user, 'web')->get(route('dashboard.home'));

        $response->assertStatus(200);
        $response->assertSee('Apa syarat Kerja Praktik?');
        $response->assertSee('sks minimal magang?');
    }

    /**
     * Test popular questions dedicated page lists items and respects toggle parameter.
     */
    public function test_popular_questions_page_renders_correctly()
    {
        // Seed 12 different questions to check limit / show all
        for ($i = 1; $i <= 12; $i++) {
            ChatLog::create([
                'message' => "Pertanyaan Unik Ke-{$i}?",
                'response' => 'Jawaban',
                'normalized_message' => "ke{$i} pertanyaan unik",
            ]);
        }

        // Default view (Limit 10)
        $response1 = $this->actingAs($this->user, 'web')->get(route('dashboard.pertanyaan-populer'));
        $response1->assertStatus(200);
        $response1->assertViewHas('popularQuestions');
        
        $questionsCount1 = count($response1->viewData('popularQuestions'));
        $this->assertEquals(10, $questionsCount1);

        // Show all view
        $response2 = $this->actingAs($this->user, 'web')->get(route('dashboard.pertanyaan-populer', ['all' => 1]));
        $response2->assertStatus(200);
        
        $questionsCount2 = count($response2->viewData('popularQuestions'));
        $this->assertEquals(12, $questionsCount2);
    }
}
