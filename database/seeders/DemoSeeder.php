<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use App\Models\Company;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use function rand;
class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $demoUser = User::factory()->create([
            'name' => 'Adminuser Walker',
            'email' => 'demo@invoicezer.com',
            'password' => Hash::make('Pa$$w0rd!'),
        ]);

        $additionalUsers = User::factory(3)->create([
            'password' => Hash::make('Pa$$w0rd!'),
        ]);

        $allUsers = collect([$demoUser])->concat($additionalUsers);

        foreach ($allUsers as $user){
            // Create company for each user
            $company = Company::factory()->forUser($user)->create();

            // Create 8-12 clients per user
            $clients = Client::factory()
                ->count(rand(8, 12))
                ->forUser($user)
                ->create();

            // Create invoices for each user
            $invoiceCount = rand(15, 25);

            // Create invoices with different statuses
            $paidCount = (int) ($invoiceCount * 0.6);      // 60% paid
            $sentCount = (int) ($invoiceCount * 0.2);       // 20% sent
            $overdueCount = (int) ($invoiceCount * 0.15);   // 15% overdue
            $draftCount = (int) ($invoiceCount * 0.03);     // 3% draft
            $cancelledCount = $invoiceCount - ($paidCount + $sentCount + $overdueCount + $draftCount); // remaining

            // Create paid invoices
            $this->createInvoicesWithItems($paidCount, $user, $company, $clients, 'paid');

            // Create sent invoices
            $this->createInvoicesWithItems($sentCount, $user, $company, $clients, 'sent');

            // Create overdue invoices
            $this->createInvoicesWithItems($overdueCount, $user, $company, $clients, 'overdue');

            // Create draft invoices
            $this->createInvoicesWithItems($draftCount, $user, $company, $clients, 'draft');

            // Create cancelled invoices
            if ($cancelledCount > 0) {
                $this->createInvoicesWithItems($cancelledCount, $user, $company, $clients, 'cancelled');
            }
        }

        $this->command->info('Demo data created successfully!');
        $this->command->info('Login credentials:');
        $this->command->info('Email: demo@invoicemanager.com');
        $this->command->info('Password: password123');
    }

    private function createInvoicesWithItems(int $count, User $user, Company $company, $clients, string $status): void
    {
        for ($i = 0; $i < $count; $i++) {
            $client = $clients->random();

            // Create invoice with specific status
            $invoice = Invoice::factory()
                ->forUser($user)
                ->forClient($client)
                ->forCompany($company)
                ->{$status}()
                ->create();

            // Create 1-6 items per invoice
            $itemCount = \rand(1, 6);
            $items = collect();

            for ($j = 0; $j < $itemCount; $j++) {
                // Mix of hourly and fixed price items
                $itemFactory = rand(0, 1)
                    ? InvoiceItem::factory()->hourlyService()
                    : InvoiceItem::factory()->fixedPrice();

                $item = $itemFactory->forInvoice($invoice)->create();
                $items->push($item);
            }

            // Calculate totals
            $this->calculateInvoiceTotals($invoice);
        }
    }

    private function calculateInvoiceTotals(Invoice $invoice): void
    {
        $subtotal = $invoice->invoiceItems()->sum('total');
        $discountAmount = $subtotal * ($invoice->discount_rate / 100);
        $taxableAmount = $subtotal - $discountAmount;
        $taxAmount = $taxableAmount * ($invoice->tax_rate / 100);
        $total = $taxableAmount + $taxAmount;

        $invoice->update([
            'subtotal' => round($subtotal, 2),
            'discount_amount' => round($discountAmount, 2),
            'tax_amount' => round($taxAmount, 2),
            'total' => round($total, 2),
        ]);
    }


}
