<?php

namespace Database\Seeders;

use App\Models\Ecrm\QuickReply;
use Illuminate\Database\Seeder;

class QuickReplySeeder extends Seeder
{
    public function run(): void
    {
        $quickReplies = [
            [
                'pertanyaan' => 'Berapa harga desain logo?',
                'jawaban' => 'Harga desain logo dimulai dari Rp 500.000 untuk paket basic. Untuk paket premium dengan beberapa variasi dan revisi unlimited, harga mulai dari Rp 1.500.000. Silakan chat admin untuk detail lebih lanjut.',
                'kategori' => 'Harga',
                'use_ai' => false,
                'order' => 1,
                'aktif' => true,
            ],
            [
                'pertanyaan' => 'Berapa lama proses desain?',
                'jawaban' => 'Proses desain biasanya memakan waktu 3-7 hari kerja, tergantung kompleksitas project. Untuk project sederhana bisa selesai dalam 3 hari, sedangkan project kompleks bisa sampai 2 minggu.',
                'kategori' => 'Timeline',
                'use_ai' => false,
                'order' => 2,
                'aktif' => true,
            ],
            [
                'pertanyaan' => 'Apakah bisa revisi?',
                'jawaban' => 'Ya, kami memberikan revisi gratis hingga 3x untuk paket basic dan unlimited revisi untuk paket premium. Revisi akan diproses dalam 1-2 hari kerja.',
                'kategori' => 'Proses',
                'use_ai' => false,
                'order' => 3,
                'aktif' => true,
            ],
            [
                'pertanyaan' => 'Format file apa saja yang diberikan?',
                'jawaban' => 'Kami menyediakan file dalam berbagai format: JPG, PNG, PDF, AI (Adobe Illustrator), dan SVG. Semua file sudah siap untuk digunakan baik untuk digital maupun print.',
                'kategori' => 'Deliverable',
                'use_ai' => false,
                'order' => 4,
                'aktif' => true,
            ],
            [
                'pertanyaan' => 'Bagaimana cara pembayaran?',
                'jawaban' => 'Pembayaran bisa dilakukan melalui transfer bank, e-wallet, atau cash. Untuk project besar, kami menerima DP 50% di awal dan 50% setelah selesai. Untuk project kecil, full payment di awal.',
                'kategori' => 'Pembayaran',
                'use_ai' => false,
                'order' => 5,
                'aktif' => true,
            ],
            [
                'pertanyaan' => 'Apakah bisa desain untuk seminar?',
                'jawaban' => 'Ya, kami melayani desain untuk berbagai kebutuhan termasuk seminar. Kami bisa membuat desain banner, backdrop, sertifikat, name tag, dan materi presentasi untuk acara seminar Anda.',
                'kategori' => 'Layanan',
                'use_ai' => true,
                'order' => 6,
                'aktif' => true,
            ],
        ];

        foreach ($quickReplies as $reply) {
            QuickReply::create($reply);
        }
    }
}

