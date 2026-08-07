<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Role;
use App\Models\User;
use App\Models\HospitalStaff;

// Show all roles grouped by hospital_id
echo "=== All roles in DB ===" . PHP_EOL;
$roles = Role::orderBy('hospital_id')->orderBy('name')->get(['id','name','hospital_id']);
foreach ($roles as $r) {
    echo "  " . str_pad($r->name, 20) . " hospital_id: " . ($r->hospital_id ?? 'NULL (global)') . PHP_EOL;
}

echo PHP_EOL . "=== Unique hospital_ids that have roles ===" . PHP_EOL;
$hospitalIds = Role::whereNotNull('hospital_id')->distinct()->pluck('hospital_id');
foreach ($hospitalIds as $hid) {
    echo "  Hospital: " . $hid . PHP_EOL;
    $hroles = Role::where('hospital_id', $hid)->pluck('name');
    echo "  Roles: " . implode(', ', $hroles->toArray()) . PHP_EOL;
    echo PHP_EOL;
}

echo "=== Hospital admin users and their hospital ===" . PHP_EOL;
$hadmins = User::whereHas('roles', fn($q) => $q->where('name', 'hospital_admin'))->get();
foreach ($hadmins as $u) {
    $staff = HospitalStaff::where('user_id', $u->id)->where('is_active', true)->first();
    echo "  User: " . $u->email . " => hospital_id: " . ($staff?->hospital_id ?? 'NONE') . PHP_EOL;
    
    if ($staff?->hospital_id) {
        $hosproles = Role::where('hospital_id', $staff->hospital_id)->pluck('name');
        echo "  Available roles for this hospital: " . implode(', ', $hosproles->toArray()) . PHP_EOL;
    }
    echo PHP_EOL;
}
