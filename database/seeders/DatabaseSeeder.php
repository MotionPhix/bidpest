<?php

namespace Database\Seeders;

use App\Models\User;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   */
  public function run(): void
  {
    // User::factory(10)->create();

    User::factory()->create([
      'name' => 'Bid Expert',
      'email' => 'bidder@example.com',
      'provider' => 'google',
      'provider_id' => Str::orderedUuid()
    ]);

    $this->call(RolesAndPermissionsSeeder::class);
  }
}
