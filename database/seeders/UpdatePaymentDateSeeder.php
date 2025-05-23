<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdatePaymentDateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            // تحديث جميع الدفعات التي ليس لها قيمة في عمود payment_date
            $updatedCount = DB::table('payments')
                ->whereNull('payment_date')
                ->update([
                    'payment_date' => DB::raw('created_at')
                ]);
            
            $this->command->info("تم تحديث {$updatedCount} دفعة بنجاح");
            
        } catch (\Exception $e) {
            Log::error('خطأ أثناء تحديث عمود payment_date: ' . $e->getMessage());
            $this->command->error('حدث خطأ أثناء تحديث البيانات: ' . $e->getMessage());
        }
    }
}
