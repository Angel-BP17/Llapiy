<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\Block;
use App\Notifications\NewBlockNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_notifications_api_endpoint_requires_authentication(): void
    {
        $response = $this->getJson('/notifications/api');

        $response->assertStatus(401);
    }

    public function test_notifications_api_endpoint_returns_unread_notifications(): void
    {
        $user = User::factory()->create();
        $block = Block::factory()->create();

        // Send a notification to the user
        $user->notify(new NewBlockNotification($block));

        $response = $this->actingAs($user)->getJson('/notifications/api');

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonPath('0.data.message', 'Se ha añadido un nuevo archivo: ' . $block->asunto);
    }

    public function test_read_notification_endpoint_requires_authentication(): void
    {
        $response = $this->getJson('/notifications/some-id');

        $response->assertStatus(401);
    }

    public function test_user_can_mark_own_notification_as_read(): void
    {
        $user = User::factory()->create();
        $block = Block::factory()->create();

        $user->notify(new NewBlockNotification($block));
        $notification = $user->unreadNotifications->first();

        $response = $this->actingAs($user)->getJson("/notifications/{$notification->id}");

        $response->assertStatus(200)
            ->assertJsonPath('message', 'Notificación marcada como leída.');

        $this->assertEquals(0, $user->fresh()->unreadNotifications()->count());
    }

    public function test_user_cannot_mark_other_users_notification_as_read(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $block = Block::factory()->create();

        $user1->notify(new NewBlockNotification($block));
        $notification = $user1->unreadNotifications->first();

        $response = $this->actingAs($user2)->getJson("/notifications/{$notification->id}");

        $response->assertStatus(404);
    }
}
