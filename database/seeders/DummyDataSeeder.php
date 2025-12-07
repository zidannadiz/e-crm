<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Ecrm\Client;
use App\Models\Ecrm\Order;
use App\Models\Ecrm\Invoice;
use App\Models\Ecrm\Payment;
use App\Models\Ecrm\ChatMessage;
use App\Models\Ecrm\QuickReply;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "🚀 Starting dummy data seeding...\n\n";

        // 1. Create Clients
        echo "📋 Creating clients...\n";
        $clients = [
            [
                'nama' => 'PT Maju Jaya',
                'email' => 'majujaya@example.com',
                'telepon' => '081234567890',
                'alamat' => 'Jl. Sudirman No. 123, Jakarta',
                'tipe' => 'perusahaan',
                'status' => 'aktif',
            ],
            [
                'nama' => 'CV Berkah Sejahtera',
                'email' => 'berkah@example.com',
                'telepon' => '081234567891',
                'alamat' => 'Jl. Gatot Subroto No. 45, Bandung',
                'tipe' => 'perusahaan',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Budi Santoso',
                'email' => 'budi.santoso@example.com',
                'telepon' => '081234567892',
                'alamat' => 'Jl. Diponegoro No. 67, Surabaya',
                'tipe' => 'individu',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Siti Nurhaliza',
                'email' => 'siti.nur@example.com',
                'telepon' => '081234567893',
                'alamat' => 'Jl. Ahmad Yani No. 89, Yogyakarta',
                'tipe' => 'individu',
                'status' => 'aktif',
            ],
            [
                'nama' => 'PT Teknologi Maju',
                'email' => 'tekno.maju@example.com',
                'telepon' => '081234567894',
                'alamat' => 'Jl. HR Rasuna Said No. 101, Jakarta',
                'tipe' => 'perusahaan',
                'status' => 'aktif',
            ],
        ];

        $createdClients = [];
        foreach ($clients as $clientData) {
            $client = Client::updateOrCreate(
                ['email' => $clientData['email']],
                $clientData
            );
            $createdClients[] = $client;
            
            // Create user for each client
            User::updateOrCreate(
                ['email' => $clientData['email']],
                [
                    'name' => $clientData['nama'],
                    'password' => Hash::make('password123'),
                    'role' => 'client',
                    'client_id' => $client->id,
                    'email_verified_at' => now(),
                ]
            );
        }
        echo "✅ Created " . count($createdClients) . " clients\n\n";

        // 2. Create Orders
        echo "📦 Creating orders...\n";
        $jenisDesain = ['logo', 'branding', 'web_design', 'ui_ux', 'print_design', 'packaging', 'social_media'];
        $statuses = ['pending', 'approved', 'in_progress', 'review', 'completed', 'cancelled'];
        
        // Check existing orders count to generate unique order numbers
        $existingOrdersCount = Order::count();
        
        $createdOrders = [];
        $orderCounter = $existingOrdersCount + 1;
        
        foreach ($createdClients as $index => $client) {
            // Create 2-3 orders per client
            $orderCount = rand(2, 3);
            for ($i = 0; $i < $orderCount; $i++) {
                $user = User::where('client_id', $client->id)->first();
                $status = $statuses[array_rand($statuses)];
                
                $order = Order::create([
                    'nomor_order' => 'ORD-' . date('Ymd') . '-' . str_pad($orderCounter, 4, '0', STR_PAD_LEFT),
                    'user_id' => $user->id,
                    'client_id' => $client->id,
                    'jenis_desain' => $jenisDesain[array_rand($jenisDesain)],
                    'deskripsi' => 'Desain ' . ucfirst($jenisDesain[array_rand($jenisDesain)]) . ' untuk keperluan branding dan marketing',
                    'kebutuhan' => 'Dibutuhkan desain yang modern, minimalis, dan eye-catching. Target audience: millennial dan gen Z.',
                    'budget' => rand(1000000, 10000000),
                    'deadline' => now()->addDays(rand(7, 30)),
                    'status' => $status,
                    'catatan_admin' => $status !== 'pending' ? 'Order telah ditinjau oleh admin' : null,
                ]);
                
                $createdOrders[] = $order;
                $orderCounter++;
                
                // Create invoice for completed orders
                if ($status === 'completed') {
                    Invoice::create([
                        'nomor_invoice' => 'INV-' . date('Ymd') . '-' . str_pad(count($createdOrders), 4, '0', STR_PAD_LEFT),
                        'order_id' => $order->id,
                        'client_id' => $client->id,
                        'tanggal_invoice' => now(),
                        'tanggal_jatuh_tempo' => now()->addDays(7),
                        'subtotal' => $order->budget,
                        'pajak' => $order->budget * 0.11,
                        'diskon' => 0,
                        'total' => $order->budget * 1.11,
                        'status' => ['sent', 'paid'][array_rand(['sent', 'paid'])],
                        'catatan' => 'Terima kasih atas kepercayaan Anda',
                        'deskripsi' => 'Invoice untuk pesanan desain',
                    ]);
                }
            }
        }
        echo "✅ Created " . count($createdOrders) . " orders\n\n";

        // 3. Create Chat Messages
        echo "💬 Creating chat messages...\n";
        $csUser = User::where('role', 'cs')->first();
        $messageCount = 0;
        
        foreach ($createdOrders as $order) {
            // Create 2-5 messages per order
            $msgCount = rand(2, 5);
            for ($i = 0; $i < $msgCount; $i++) {
                $isFromClient = $i % 2 === 0;
                
                ChatMessage::create([
                    'order_id' => $order->id,
                    'user_id' => $isFromClient ? $order->user_id : ($csUser ? $csUser->id : 1),
                    'message' => $isFromClient 
                        ? 'Halo, saya ingin menanyakan progress dari pesanan saya. Kapan bisa selesai?'
                        : 'Terima kasih atas pertanyaan Anda. Tim kami sedang mengerjakan pesanan Anda dan akan segera selesai.',
                    'is_read' => rand(0, 1) === 1,
                    'created_at' => now()->subDays(rand(0, 7))->subHours(rand(0, 23)),
                ]);
                $messageCount++;
            }
        }
        echo "✅ Created " . $messageCount . " chat messages\n\n";

        // 4. Create Quick Replies
        echo "⚡ Creating quick replies...\n";
        $quickReplies = [
            [
                'judul' => 'Salam Pembuka',
                'isi' => 'Halo! Terima kasih telah menghubungi kami. Ada yang bisa kami bantu?',
                'kategori' => 'greeting',
            ],
            [
                'judul' => 'Konfirmasi Order',
                'isi' => 'Pesanan Anda telah kami terima dan sedang dalam proses. Kami akan menginformasikan jika ada update.',
                'kategori' => 'order',
            ],
            [
                'judul' => 'Request Revisi',
                'isi' => 'Silakan kirimkan detail revisi yang Anda inginkan. Kami akan segera memprosesnya.',
                'kategori' => 'revision',
            ],
            [
                'judul' => 'Pengingat Pembayaran',
                'isi' => 'Reminder: Pembayaran untuk invoice Anda akan jatuh tempo dalam 3 hari. Mohon segera dilakukan pembayaran.',
                'kategori' => 'payment',
            ],
            [
                'judul' => 'Terima Kasih',
                'isi' => 'Terima kasih atas kepercayaan Anda menggunakan layanan kami. Jangan ragu untuk menghubungi kami kembali!',
                'kategori' => 'closing',
            ],
        ];

        foreach ($quickReplies as $qr) {
            QuickReply::create($qr);
        }
        echo "✅ Created " . count($quickReplies) . " quick replies\n\n";

        // 5. Create Payments
        echo "💰 Creating payments...\n";
        $paidInvoices = Invoice::where('status', 'paid')->get();
        $paymentCount = 0;
        
        foreach ($paidInvoices as $invoice) {
            Payment::create([
                'invoice_id' => $invoice->id,
                'client_id' => $invoice->client_id,
                'jumlah' => $invoice->total,
                'metode' => ['transfer', 'cash', 'credit_card'][array_rand(['transfer', 'cash', 'credit_card'])],
                'tanggal' => now()->subDays(rand(1, 10)),
                'status' => 'verified',
                'bukti_transfer' => 'payments/bukti_' . time() . '.jpg',
                'catatan' => 'Pembayaran telah diverifikasi',
            ]);
            $paymentCount++;
        }
        echo "✅ Created " . $paymentCount . " payments\n\n";

        echo "🎉 Dummy data seeding completed successfully!\n";
        echo "\n📊 Summary:\n";
        echo "   - Clients: " . count($createdClients) . "\n";
        echo "   - Orders: " . count($createdOrders) . "\n";
        echo "   - Messages: " . $messageCount . "\n";
        echo "   - Quick Replies: " . count($quickReplies) . "\n";
        echo "   - Payments: " . $paymentCount . "\n";
    }
}

