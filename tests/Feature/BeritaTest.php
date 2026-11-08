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

    public function test_admin_can_update_berita(): void
    {
        $admin = \App\Models\User::factory()->create(['role' => 'admin']);

        $kategori1 = KategoriBerita::create(['nama' => 'Kategori 1', 'slug' => 'kategori-1']);
        $kategori2 = KategoriBerita::create(['nama' => 'Kategori 2', 'slug' => 'kategori-2']);

        $berita = Berita::create([
            'kategori_berita_id' => $kategori1->id,
            'title' => 'Judul Awal',
            'slug' => 'judul-awal',
            'content' => 'Isi awal berita.',
            'is_published' => true,
            'views' => 10,
        ]);

        $response = $this->actingAs($admin)->put('/admin/berita/' . $berita->id, [
            'title' => 'Judul Baru Diperbarui',
            'kategori_berita_id' => $kategori2->id,
            'content' => 'Isi artikel setelah diedit.',
            'is_published' => '1',
            'views' => 25,
        ]);

        $response->assertRedirect();
        $berita->refresh();
        $this->assertEquals('Judul Baru Diperbarui', $berita->title);
        $this->assertEquals('judul-baru-diperbarui', $berita->slug);
        $this->assertEquals($kategori2->id, $berita->kategori_berita_id);
        $this->assertEquals('Isi artikel setelah diedit.', $berita->content);
        $this->assertEquals(25, $berita->views);
    }
}
