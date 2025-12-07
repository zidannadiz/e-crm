# 📊 Load Balancing Chat - Dokumentasi Lengkap

## 🎯 Overview

Sistem load balancing chat ini dirancang untuk mendistribusikan customer secara otomatis dan merata ke 20 petugas (10 Admin + 10 CS) dengan algoritma yang efisien, aman dari race condition, dan selalu menjaga keseimbangan.

## 🏗️ Arsitektur

### Database Schema

**Table: `ecrm_chat_sessions`**
```sql
- id (primary key)
- order_id (foreign key ke ecrm_orders)
- customer_id (foreign key ke users - customer)
- agent_id (foreign key ke users - admin/cs)
- status (enum: 'waiting', 'active', 'ended')
- assigned_at (timestamp)
- ended_at (timestamp)
- created_at, updated_at
```

### Komponen Utama

1. **ChatSession Model** (`app/Models/Ecrm/ChatSession.php`)
   - Model untuk mengelola sesi chat
   - Relationships: order, customer, agent

2. **ChatLoadBalancerService** (`app/Services/ChatLoadBalancerService.php`)
   - Service utama untuk algoritma load balancing
   - Fungsi: `assignAgentToCustomer()`, `endChatSession()`, `getLoadStatistics()`

3. **ChatController** (Updated)
   - Auto-assign agent ketika customer membuka chat pertama kali

4. **ChatLoadBalancerController**
   - API endpoints untuk monitoring dan management

## 🔄 Algoritma Load Balancing

### 1. **assignAgentToCustomer()**

**Langkah-langkah:**
1. Cek apakah session sudah ada (menggunakan database lock untuk race condition)
2. Jika sudah ada, return agent yang sudah ditugaskan
3. Ambil semua agent yang available (admin + cs)
4. Hitung jumlah customer aktif per agent dengan query optimized
5. Pilih agent dengan beban paling sedikit
6. Jika ada beberapa agent dengan beban sama, pilih yang ID-nya paling kecil (fallback logic)
7. Buat/update chat session dengan agent yang dipilih

**Query Optimized:**
```php
ChatSession::whereIn('agent_id', $agentIds)
    ->where('status', 'active')
    ->select('agent_id', DB::raw('COUNT(*) as active_count'))
    ->groupBy('agent_id')
    ->pluck('active_count', 'agent_id')
```

### 2. **Race Condition Prevention**

Menggunakan **Database Transaction dengan Lock**:
```php
DB::transaction(function () {
    // lockForUpdate() untuk mencegah concurrent access
    $session = ChatSession::lockForUpdate()->first();
    // ... assign logic
});
```

### 3. **Fallback Logic**

Jika 2+ agent memiliki beban sama:
- Pilih agent dengan **ID terkecil** (yang terdaftar paling awal)
- Implementasi: `$candidates->sortBy('id')->first()`

## 📝 Contoh Penggunaan

### 1. Auto-Assign (Otomatis)

Ketika customer membuka chat, sistem otomatis assign agent:

```php
// Di ChatController::index()
if (Auth::user()->role === 'client') {
    $assignedAgent = $this->loadBalancer->assignAgentToCustomer($order->id, Auth::id());
}
```

### 2. Manual Assign (Jika Perlu)

```php
use App\Services\ChatLoadBalancerService;

$loadBalancer = app(ChatLoadBalancerService::class);
$agent = $loadBalancer->assignAgentToCustomer($orderId, $customerId);

if ($agent) {
    echo "Agent assigned: " . $agent->name;
}
```

### 3. End Chat Session

```php
$loadBalancer->endChatSession($orderId, $customerId);
```

### 4. Get Load Statistics

```php
$statistics = $loadBalancer->getLoadStatistics();

// Output:
// [
//     [
//         'agent_id' => 1,
//         'agent_name' => 'Admin 1',
//         'agent_email' => 'admin1@ecrm.com',
//         'agent_role' => 'admin',
//         'active_customers' => 5
//     ],
//     ...
// ]
```

## 🌐 API Endpoints

### 1. Get Load Statistics
```
GET /ecrm/chat/load-balancer/statistics
```
**Access:** Admin only

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "agent_id": 1,
            "agent_name": "Admin 1",
            "agent_email": "admin1@ecrm.com",
            "agent_role": "admin",
            "active_customers": 5
        }
    ],
    "summary": {
        "total_agents": 20,
        "total_active_customers": 100,
        "average_load": 5.0,
        "min_load": 4,
        "max_load": 6
    }
}
```

### 2. End Chat Session
```
POST /ecrm/chat/load-balancer/order/{orderId}/end-session
Body: { "customer_id": 123 }
```
**Access:** Admin, CS, atau Customer (untuk session sendiri)

### 3. Reassign Agent
```
POST /ecrm/chat/load-balancer/order/{orderId}/reassign
Body: { "customer_id": 123 }
```
**Access:** Admin only

## 🔒 Keamanan & Race Condition

### 1. Database Locking
- Menggunakan `lockForUpdate()` untuk mencegah race condition
- Transaction memastikan atomicity

### 2. Access Control
- Customer hanya bisa end session sendiri
- Admin bisa reassign dan end semua session
- CS bisa end session yang ditugaskan ke mereka

## ⚡ Optimasi Performa

### 1. Index Database
```php
$table->index('agent_id');
$table->index('customer_id');
$table->index('status');
$table->index(['agent_id', 'status']); // Composite index
```

### 2. Query Optimization
- Single query untuk menghitung load semua agent
- Menggunakan `groupBy` dan `COUNT()` di database level
- Tidak melakukan loop di PHP

### 3. Caching (Opsional)
Jika perlu, bisa ditambahkan Redis cache untuk load statistics:
```php
Cache::remember('agent_loads', 60, function() {
    return $this->calculateAgentLoads($agentIds);
});
```

## 📊 Monitoring

### Dashboard Load Balancing (Bisa ditambahkan)

```php
// Di admin dashboard
$statistics = $loadBalancer->getLoadStatistics();

// Tampilkan:
// - Total agents: 20
// - Total active customers: 100
// - Average load: 5.0
// - Min load: 4
// - Max load: 6
// - Load distribution chart
```

## 🧪 Testing Scenario

### Scenario 1: 100 Customer, 20 Agents
- Expected: Setiap agent mendapat ~5 customer
- Actual: Distribusi merata dengan selisih maksimal 1

### Scenario 2: Concurrent Requests
- 10 customer membuka chat bersamaan
- Expected: Tidak ada race condition, distribusi tetap merata
- Actual: Database lock mencegah race condition

### Scenario 3: Agent Offline
- Agent tidak aktif (tidak ada di available agents)
- Expected: Customer tidak di-assign ke agent tersebut
- Actual: Hanya active agents yang dipertimbangkan

## 🚀 Deployment

### 1. Run Migration
```bash
php artisan migrate
```

### 2. Test Load Balancing
```bash
# Test dengan 100 customer
php artisan tinker
>>> $loadBalancer = app(\App\Services\ChatLoadBalancerService::class);
>>> for($i=1; $i<=100; $i++) {
...     $loadBalancer->assignAgentToCustomer(1, $i);
... }
>>> $loadBalancer->getLoadStatistics();
```

### 3. Monitor
- Akses `/ecrm/chat/load-balancer/statistics` sebagai admin
- Pastikan distribusi merata

## 📌 Best Practices

1. **Selalu gunakan transaction** untuk operasi assign
2. **Monitor load statistics** secara berkala
3. **Log semua assignment** untuk audit trail
4. **Handle edge cases** (no agents available, etc.)
5. **Test dengan concurrent requests** sebelum production

## 🔧 Troubleshooting

### Problem: Distribusi tidak merata
**Solution:** Pastikan menggunakan `lockForUpdate()` dan transaction

### Problem: Race condition masih terjadi
**Solution:** Pastikan semua operasi assign dalam transaction yang sama

### Problem: Query lambat
**Solution:** Pastikan index sudah dibuat, gunakan composite index

## 📚 Referensi

- Laravel Database Transactions: https://laravel.com/docs/database#database-transactions
- Database Locking: https://laravel.com/docs/queries#pessimistic-locking
- Load Balancing Algorithms: Round Robin, Least Connections, etc.

