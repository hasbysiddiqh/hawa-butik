<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Kategori;
use App\Models\Pelanggan;
use App\Models\Produk;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProdukControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('produk');
        $this->actingAs(Admin::factory()->create(), 'admin');
    }

    /** Gambar PNG 1x1 yang valid, supaya tes tidak bergantung pada ekstensi GD. */
    private function foto(string $nama = 'foto.png'): UploadedFile
    {
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==');

        return UploadedFile::fake()->createWithContent($nama, $png);
    }

    /** @return array<string, mixed> */
    private function dataValid(Kategori $kategori, array $timpa = []): array
    {
        return array_merge([
            'id_kategori' => $kategori->id_kategori,
            'nama_produk' => 'Kebaya Brokat',
            'deskripsi' => 'Kebaya klasik',
            'harga_sewa' => 350000,
            'stok' => 4,
            'status_produk' => 'aktif',
        ], $timpa);
    }

    public function test_guest_and_pelanggan_cannot_manage_produk(): void
    {
        $this->app['auth']->guard('admin')->logout();
        $kategori = Kategori::factory()->create();

        $this->get(route('admin.produk.index'))->assertRedirect(route('login'));
        $this->post(route('admin.produk.store'), $this->dataValid($kategori))->assertRedirect(route('login'));

        $this->actingAs(Pelanggan::factory()->create(), 'pelanggan')
            ->get(route('admin.produk.create'))
            ->assertRedirect(route('login'));

        $this->assertDatabaseCount('produks', 0);
    }

    public function test_index_lists_all_produk_including_nonaktif_and_can_search(): void
    {
        Produk::factory()->create(['nama_produk' => 'Gamis Tenun']);
        Produk::factory()->nonaktif()->create(['nama_produk' => 'Blouse Tersembunyi']);

        $this->get(route('admin.produk.index'))
            ->assertOk()
            ->assertSee('Gamis Tenun')
            ->assertSee('Blouse Tersembunyi');

        $this->get(route('admin.produk.index', ['cari' => 'Gamis']))
            ->assertSee('Gamis Tenun')
            ->assertDontSee('Blouse Tersembunyi');
    }

    public function test_create_page_renders(): void
    {
        Kategori::factory()->create(['nama_kategori' => 'Kebaya']);

        $this->get(route('admin.produk.create'))
            ->assertOk()
            ->assertSee('Kebaya');
    }

    public function test_admin_can_add_produk_with_photo(): void
    {
        $kategori = Kategori::factory()->create();

        $this->post(route('admin.produk.store'), $this->dataValid($kategori) + ['foto_produk' => $this->foto()])
            ->assertRedirect(route('admin.produk.index'))
            ->assertSessionHas('success');

        $produk = Produk::firstOrFail();
        $this->assertSame('Kebaya Brokat', $produk->nama_produk);
        $this->assertSame(350000, $produk->harga_sewa);
        $this->assertNotNull($produk->foto_produk);
        Storage::disk('produk')->assertExists($produk->foto_produk);
    }

    public function test_photo_is_optional(): void
    {
        $this->post(route('admin.produk.store'), $this->dataValid(Kategori::factory()->create()))
            ->assertSessionHasNoErrors();

        $this->assertNull(Produk::firstOrFail()->foto_produk);
    }

    public function test_validation_rejects_bad_input(): void
    {
        $kategori = Kategori::factory()->create();

        $this->post(route('admin.produk.store'), [])
            ->assertSessionHasErrors(['id_kategori', 'nama_produk', 'harga_sewa', 'stok', 'status_produk']);

        $this->post(route('admin.produk.store'), $this->dataValid($kategori, ['harga_sewa' => -1]))
            ->assertSessionHasErrors('harga_sewa');
        $this->post(route('admin.produk.store'), $this->dataValid($kategori, ['stok' => -5]))
            ->assertSessionHasErrors('stok');
        $this->post(route('admin.produk.store'), $this->dataValid($kategori, ['id_kategori' => 9999]))
            ->assertSessionHasErrors('id_kategori');
        $this->post(route('admin.produk.store'), $this->dataValid($kategori, ['status_produk' => 'dihapus']))
            ->assertSessionHasErrors('status_produk');

        $this->assertDatabaseCount('produks', 0);
    }

    public function test_photo_must_be_an_image(): void
    {
        $kategori = Kategori::factory()->create();

        $this->post(route('admin.produk.store'), $this->dataValid($kategori) + [
            'foto_produk' => UploadedFile::fake()->createWithContent('skrip.php', '<?php echo 1;'),
        ])->assertSessionHasErrors('foto_produk');

        $this->post(route('admin.produk.store'), $this->dataValid($kategori) + [
            'foto_produk' => UploadedFile::fake()->createWithContent('catatan.txt', 'bukan gambar'),
        ])->assertSessionHasErrors('foto_produk');

        $this->assertDatabaseCount('produks', 0);
        $this->assertSame([], Storage::disk('produk')->allFiles());
    }

    public function test_photo_larger_than_two_megabytes_is_rejected(): void
    {
        $besar = UploadedFile::fake()->create('besar.png', 3000, 'image/png');

        $this->post(route('admin.produk.store'), $this->dataValid(Kategori::factory()->create()) + ['foto_produk' => $besar])
            ->assertSessionHasErrors('foto_produk');
    }

    public function test_admin_can_update_data_and_manage_stock_and_status(): void
    {
        $produk = Produk::factory()->create(['stok' => 5, 'status_produk' => 'aktif']);

        $this->get(route('admin.produk.edit', $produk))->assertOk();

        $this->put(route('admin.produk.update', $produk), $this->dataValid($produk->kategori, [
            'nama_produk' => 'Nama Baru',
            'stok' => 0,
            'status_produk' => 'nonaktif',
        ]))->assertRedirect(route('admin.produk.index'));

        $produk->refresh();
        $this->assertSame('Nama Baru', $produk->nama_produk);
        $this->assertSame(0, $produk->stok);
        $this->assertSame('nonaktif', $produk->status_produk);
    }

    public function test_update_without_new_photo_keeps_the_old_one(): void
    {
        $this->post(route('admin.produk.store'), $this->dataValid(Kategori::factory()->create()) + ['foto_produk' => $this->foto()]);
        $produk = Produk::firstOrFail();
        $fotoLama = $produk->foto_produk;

        $this->put(route('admin.produk.update', $produk), $this->dataValid($produk->kategori, ['nama_produk' => 'Ganti Nama']));

        $this->assertSame($fotoLama, $produk->fresh()->foto_produk);
        Storage::disk('produk')->assertExists($fotoLama);
    }

    public function test_new_photo_replaces_and_deletes_the_old_file(): void
    {
        $this->post(route('admin.produk.store'), $this->dataValid(Kategori::factory()->create()) + ['foto_produk' => $this->foto()]);
        $produk = Produk::firstOrFail();
        $fotoLama = $produk->foto_produk;

        $this->put(route('admin.produk.update', $produk), $this->dataValid($produk->kategori) + ['foto_produk' => $this->foto('baru.png')]);

        $fotoBaru = $produk->fresh()->foto_produk;
        $this->assertNotSame($fotoLama, $fotoBaru);
        Storage::disk('produk')->assertExists($fotoBaru);
        Storage::disk('produk')->assertMissing($fotoLama);
    }

    public function test_deleting_produk_removes_the_record_and_its_photo(): void
    {
        $this->post(route('admin.produk.store'), $this->dataValid(Kategori::factory()->create()) + ['foto_produk' => $this->foto()]);
        $produk = Produk::firstOrFail();
        $foto = $produk->foto_produk;

        $this->delete(route('admin.produk.destroy', $produk))
            ->assertRedirect(route('admin.produk.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseCount('produks', 0);
        Storage::disk('produk')->assertMissing($foto);
    }
}
