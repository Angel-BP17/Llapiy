<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_guest_is_redirected_to_docs_from_home(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/docs');
    }

    public function test_doc_route_returns_documentation_view(): void
    {
        $response = $this->get('/doc');

        $response->assertStatus(200);
        $response->assertSee('LlapiyAPI - Documentacion');
    }
}
