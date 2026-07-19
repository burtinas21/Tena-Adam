<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$tables = [
    'review_ratings', 'appointment_slots', 'queues', 'queue_call_logs',
    'telehealth_sessions', 'telehealth_attendances', 'audit_logs',
    'notifications', 'invoices', 'payments', 'refunds', 'reports',
    'symptoms', 'symptom_analytics', 'symptom_department_mappings',
    'doctor_specializations', 'medications',
];

foreach ($tables as $t) {
    try {
        $cols = \Schema::getColumnListing($t);
        $count = count($cols);
        // Flag tables that likely only have stub columns (id + timestamps = 3)
        $flag = $count <= 3 ? ' *** BROKEN (only ' . $count . ' cols) ***' : '';
        echo $t . ' [' . $count . ' cols]: ' . implode(', ', $cols) . $flag . PHP_EOL;
    } catch (Exception $e) {
        echo $t . ': TABLE DOES NOT EXIST' . PHP_EOL;
    }
}
