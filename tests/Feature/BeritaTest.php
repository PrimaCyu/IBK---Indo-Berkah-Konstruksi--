<?php

namespace Tests\Feature;

use App\Models\Berita;
use App\Models\KategoriBerita;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BeritaTest extends TestCase
{
    use RefreshDatabase;

    public function test_berita_index_page_returns_successful_response(): void
    {
        $kategori = KategoriBerita::create([
            'nama' => 'Arsitektur',
            'slug' => 'arsitektur',
        ]);

        Berita::create([
            'kategori_berita_id' => $kategori->id,
            'title' => 'Inovasi Beton Ringan Ramah Lingkungan',
            'slug' => 'inovasi-beton-ringan-ramah-lingkungan',
            'content' => 'Konten pengujian berita konstruksi dan teknologi terbaru.',
            'is_published' => true,
            'views' => 42,
        ]);

        $response = $this->get('/berita');

        $response->assertStatus(200);
        $response->assertSee('Inovasi Beton Ringan Ramah Lingkungan');
        $response->assertSee('42');
        $response->assertSee('Arsitektur');
    }

    public function test_berita_detail_page_increments_views(): void
    {
        $kategori = KategoriBerita::create([
            'nama' => 'Teknologi',
            'slug' => 'teknologi',
        ]);

        $berita = Berita::create([
            'kategori_berita_id' => $kategori->id,
            'title' => 'Teknologi Smart Building Modern',
            'slug' => 'teknologi-smart-building-modern',
            'content' => 'Pembahasan smart building terkini.',
            'is_published' => true,
            'views' => 10,
        ]);

        $response = $this->get('/berita/' . $berita->slug);

        $response->assertStatus(200);
        $response->assertSee('Teknologi Smart Building Modern');
        $this->assertEquals(11, $berita->fresh()->views);
    }
}
