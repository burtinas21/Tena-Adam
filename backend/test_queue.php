<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Services\QueueService;

$svc = app(QueueService::class);

$doctorId = DB::table('queue')
    ->select('doctor_id', DB::raw('COUNT(*) as cnt'))
    ->groupBy('doctor_id')->orderByDesc('cnt')->value('doctor_id');
$date = DB::table('queue')->where('doctor_id', $doctorId)->value('queue_date');

// Reset
DB::table('queue')->where('doctor_id', $doctorId)->whereRaw('queue_date = ?', [$date])
    ->update(['status' => 'waiting', 'called_at' => null, 'started_at' => null, 'ended_at' => null]);

echo "Doctor: $doctorId  Date: $date\n";
echo "Waiting: " . DB::table('queue')->where('doctor_id',$doctorId)->whereRaw('queue_date = ?',[$date])->where('status','waiting')->count() . "\n\n";

echo "=== Call #1 (expect: called) ===\n";
$r1 = $svc->callNextPatient($doctorId, $date);
echo "  message: " . $r1['message'] . "\n";
echo "  Q#" . ($r1['current_patient']['queue_number'] ?? '-') . " → " . ($r1['current_patient']['status'] ?? '-') . "\n\n";

echo "=== Call #2 (expect: already in consultation guard) ===\n";
$r2 = $svc->callNextPatient($doctorId, $date);
echo "  message: " . $r2['message'] . "\n";
echo "  active_consultation Q#" . ($r2['active_consultation']['queue_number'] ?? '-') . "\n\n";

echo "=== Complete Q#1 ===\n";
$r3 = $svc->completeConsultation($r1['current_patient']['id']);
echo "  " . $r3['message'] . "\n\n";

echo "=== Call #3 (expect: next patient called) ===\n";
$r4 = $svc->callNextPatient($doctorId, $date);
echo "  message: " . $r4['message'] . "\n";
echo "  Q#" . ($r4['current_patient']['queue_number'] ?? '-') . "\n\n";

echo "=== Final state ===\n";
foreach (DB::table('queue')->where('doctor_id',$doctorId)->whereRaw('queue_date=?',[$date])->orderBy('queue_number')->get(['queue_number','status']) as $e) {
    echo "  Q#{$e->queue_number} [{$e->status}]\n";
}
echo "\n✅ DONE\n";
