<?php

namespace Tests\Feature;

use App\Models\Recap;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecapTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function payload(): array
    {
        return [
            'name' => 'Rekapan PO',
            'columns' => [
                ['key' => 'pelanggan', 'label' => 'Pelanggan', 'type' => 'text'],
                ['key' => 'qty', 'label' => 'Qty', 'type' => 'number', 'total' => '1'],
                ['key' => 'harga', 'label' => 'Harga', 'type' => 'number', 'total' => '1'],
            ],
            'rows' => [
                ['key' => 'r1', 'cells' => ['pelanggan' => 'Si A', 'qty' => '2', 'harga' => '30000']],
                ['key' => 'r2', 'cells' => ['pelanggan' => 'Si B', 'qty' => '1', 'harga' => '45000']],
            ],
        ];
    }

    public function test_admin_can_create_and_view_recap(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();

        $this->actingAs($admin)
            ->post(route('admin.recaps.store'), $this->payload())
            ->assertSessionHasNoErrors();

        $recap = Recap::firstOrFail();
        $this->assertSame('Rekapan PO', $recap->name);
        $this->assertCount(3, $recap->columns);
        $this->assertCount(2, $recap->rows);

        // Grand total = 2 rows of qty (3) + harga (75000) = 75003.
        $totals = $recap->totals();
        $this->assertSame(3.0, $totals['perColumn']['qty']);
        $this->assertSame(75000.0, $totals['perColumn']['harga']);
        $this->assertSame(75003.0, $totals['grand']);

        $this->actingAs($admin)
            ->get(route('admin.recaps.show', $recap))
            ->assertOk()
            ->assertSee('Rekapan PO')
            ->assertSee('Grand Total')
            ->assertSee('Rp 75.003');
    }

    public function test_update_replaces_columns_and_rows(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();
        $this->actingAs($admin)->post(route('admin.recaps.store'), $this->payload());

        $recap = Recap::firstOrFail();
        $updated = $this->payload();
        $updated['name'] = 'Rekapan PO Revisi';
        $updated['columns'] = [
            ['key' => 'pelanggan', 'label' => 'Pelanggan', 'type' => 'text'],
            ['key' => 'harga', 'label' => 'Harga', 'type' => 'number', 'total' => '1'],
        ];
        $updated['rows'] = [
            ['key' => 'r1', 'cells' => ['pelanggan' => 'Si A', 'harga' => '10000']],
        ];

        $this->actingAs($admin)
            ->put(route('admin.recaps.update', $recap), $updated)
            ->assertSessionHasNoErrors();

        $recap->refresh();
        $this->assertSame('Rekapan PO Revisi', $recap->name);
        $this->assertCount(2, $recap->columns);
        $this->assertSame(10000.0, $recap->totals()['grand']);
    }

    public function test_blank_label_columns_are_dropped_and_empty_rows_skipped(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();

        $payload = [
            'name' => 'Rekapan Bersih',
            'columns' => [
                ['key' => 'a', 'label' => 'Pelanggan', 'type' => 'text'],
                ['key' => 'b', 'label' => '   ', 'type' => 'text'],
            ],
            'rows' => [
                ['key' => 'r1', 'cells' => ['a' => 'Si A']],
                ['key' => 'r2', 'cells' => ['a' => '']],
            ],
        ];

        $this->actingAs($admin)->post(route('admin.recaps.store'), $payload)->assertSessionHasNoErrors();

        $recap = Recap::firstOrFail();
        $this->assertCount(1, $recap->columns);
        $this->assertCount(1, $recap->rows);
    }

    public function test_requires_at_least_one_column_with_label(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();

        $this->actingAs($admin)->post(route('admin.recaps.store'), [
            'name' => 'Kosong',
            'columns' => [['key' => 'a', 'label' => '', 'type' => 'text']],
        ])->assertSessionHasErrors('columns');
    }

    public function test_admin_can_delete_recap(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();
        $this->actingAs($admin)->post(route('admin.recaps.store'), $this->payload());

        $recap = Recap::firstOrFail();
        $this->actingAs($admin)
            ->delete(route('admin.recaps.destroy', $recap))
            ->assertRedirect(route('admin.recaps.index'));

        $this->assertDatabaseMissing('recaps', ['id' => $recap->id]);
    }

    public function test_non_admin_cannot_access_recaps(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $this->actingAs($customer)->get(route('admin.recaps.index'))->assertForbidden();
        $this->actingAs($customer)->post(route('admin.recaps.store'), [])->assertForbidden();
    }
}
