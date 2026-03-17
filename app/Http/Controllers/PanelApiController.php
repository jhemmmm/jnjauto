<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\Size;
use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use App\Models\InventoryLog;
use App\Models\Notification;
use App\Models\Setting;
use App\Models\User;
use App\Models\Role;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class PanelApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Dashboard data
     */
    public function dashboard()
    {
        $today = today();

        $openingTime = Setting::get('opening_time', '07:00');
        $closingTime = Setting::get('closing_time', '17:00');
        $slotDuration = (int) Setting::get('slot_duration', 30);
        $capacity = (int) Setting::get('slot_capacity', 2);

        // Calculate total slots from settings
        $openHour = (int) substr($openingTime, 0, 2);
        $openMin  = (int) substr($openingTime, 3, 2);
        $closeHour = (int) substr($closingTime, 0, 2);
        $closeMin  = (int) substr($closingTime, 3, 2);
        $totalMinutes = ($closeHour * 60 + $closeMin) - ($openHour * 60 + $openMin);
        $totalSlots = max(1, (int) floor($totalMinutes / $slotDuration));

        $todayAppointments = Appointment::with(['service', 'size'])
            ->whereDate('date', $today)
            ->orderBy('time')
            ->get();

        $todayRevenue = Appointment::completed()
            ->whereDate('completed_at', $today)
            ->sum('amount');

        $yesterdayRevenue = Appointment::completed()
            ->whereDate('completed_at', $today->copy()->subDay())
            ->sum('amount');

        $revenueChange = $yesterdayRevenue > 0
            ? round((($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100, 1)
            : ($todayRevenue > 0 ? 100 : 0);

        $bookedSlots = Appointment::whereDate('date', $today)
            ->whereNotIn('status', ['cancelled'])
            ->count();

        $completedToday = Appointment::whereDate('date', $today)
            ->where('status', 'completed')
            ->count();

        $inProgressCount = Appointment::whereDate('date', $today)
            ->where('status', 'in_progress')
            ->count();

        // Top services today
        $topServices = Appointment::whereDate('date', $today)
            ->where('status', 'completed')
            ->selectRaw('service_id, COUNT(*) as booking_count, SUM(amount) as total_revenue')
            ->groupBy('service_id')
            ->with('service')
            ->orderByDesc('total_revenue')
            ->get()
            ->map(fn($item) => [
                'name' => $item->service->name ?? 'Unknown',
                'description' => $item->service->description ?? '',
                'bookings' => $item->booking_count,
                'revenue' => (float) $item->total_revenue,
            ]);

        // Recent appointments
        $recentAppointments = Appointment::with(['service', 'size'])
            ->whereDate('date', $today)
            ->orderBy('time', 'desc')
            ->limit(8)
            ->get()
            ->map(fn($a) => [
                'id' => $a->id,
                'customer_name' => $a->customer_name,
                'customer_phone' => $a->customer_phone,
                'service' => $a->service->name ?? '—',
                'size' => $a->size->name ?? '—',
                'time' => Carbon::createFromFormat('H:i', $a->time)->format('h:i A'),
                'status' => $a->status,
            ]);

        // Time slots
        $slotCounts = Appointment::whereDate('date', $today)
            ->whereNotIn('status', ['cancelled'])
            ->selectRaw("TIME_FORMAT(time, '%H:%i') as slot_time, COUNT(*) as count")
            ->groupBy('slot_time')
            ->pluck('count', 'slot_time');

        $slots = [];
        $cursor = $openHour * 60 + $openMin;
        $end = $closeHour * 60 + $closeMin;
        while ($cursor < $end) {
            $t = sprintf('%02d:%02d', intdiv($cursor, 60), $cursor % 60);
            $used = $slotCounts[$t] ?? 0;
            $slots[] = [
                'time' => Carbon::createFromFormat('H:i', $t)->format('h:i A'),
                'available' => $capacity - $used,
                'capacity' => $capacity,
                'status' => $used >= $capacity ? 'full' : ($used > 0 ? 'partial' : 'open'),
            ];
            $cursor += $slotDuration;
        }

        return response()->json([
            'todayRevenue' => (float) $todayRevenue,
            'revenueChange' => $revenueChange,
            'bookedSlots' => $bookedSlots,
            'totalSlots' => $totalSlots,
            'completedToday' => $completedToday,
            'inProgressCount' => $inProgressCount,
            'topServices' => $topServices,
            'recentAppointments' => $recentAppointments,
            'slots' => $slots,
            'today' => now()->format('M d, Y'),
            'weeklyRevenue' => $this->weeklyRevenue(),
            'inventoryAlerts' => $this->inventoryAlerts(),
        ]);
    }

    /**
     * Last-7-day revenue sparkline data
     */
    private function weeklyRevenue(): array
    {
        $period = CarbonPeriod::create(now()->subDays(6)->startOfDay(), now());
        $dailyTotals = Appointment::completed()
            ->whereBetween('completed_at', [now()->subDays(6)->startOfDay(), now()->endOfDay()])
            ->selectRaw('DATE(completed_at) as d, SUM(amount) as total')
            ->groupBy('d')
            ->pluck('total', 'd');

        $result = [];
        foreach ($period as $date) {
            $key = $date->format('Y-m-d');
            $result[] = [
                'day'   => $date->format('D'),
                'date'  => $date->format('M d'),
                'total' => (float) ($dailyTotals[$key] ?? 0),
            ];
        }
        return $result;
    }

    /**
     * Low stock / out of stock items for the dashboard
     */
    private function inventoryAlerts(): array
    {
        return InventoryItem::with('category')
            ->whereIn('status', ['low_stock', 'out_of_stock'])
            ->orderByRaw("FIELD(status, 'out_of_stock', 'low_stock')")
            ->limit(6)
            ->get()
            ->map(fn($i) => [
                'id'            => $i->id,
                'name'          => $i->name,
                'category'      => $i->category->name ?? '—',
                'quantity'      => $i->quantity,
                'unit'          => $i->unit,
                'reorder_level' => $i->reorder_level,
                'status'        => $i->status,
            ])
            ->toArray();
    }

    /**
     * Appointments list with filters
     */
    public function appointments(Request $request)
    {
        $query = Appointment::with(['service', 'size']);

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('date')) {
            $query->where('date', $request->date);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_email', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        $appointments = $query->orderBy('date', 'desc')->orderBy('time', 'desc')->paginate(10)->withQueryString();

        $stats = [
            'today' => Appointment::whereDate('date', today())->count(),
            'scheduled' => Appointment::where('status', 'scheduled')->count(),
            'completed' => Appointment::where('status', 'completed')->count(),
            'cancelled' => Appointment::where('status', 'cancelled')->count(),
        ];

        $services = Service::orderBy('name')->get();
        $sizes = Size::orderBy('name')->get();

        return response()->json([
            'appointments' => $appointments,
            'stats' => $stats,
            'services' => $services,
            'sizes' => $sizes,
        ]);
    }

    /**
     * Store appointment (AJAX)
     */
    public function storeAppointment(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'service_id' => 'required|exists:services,id',
            'size_id' => 'required|exists:sizes,id',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        // Validate time is within operating hours
        $openingTime = Setting::get('opening_time', '07:00');
        $closingTime = Setting::get('closing_time', '17:00');

        if ($request->time < $openingTime || $request->time >= $closingTime) {
            return response()->json(['message' => 'Selected time is outside operating hours (' . $openingTime . ' – ' . $closingTime . ').'], 422);
        }

        // Validate slot capacity
        $slotCapacity = (int) Setting::get('slot_capacity', 2);
        $existingCount = Appointment::where('date', $request->date)
            ->where('time', sprintf('%s:00', $request->time))
            ->whereNotIn('status', ['cancelled'])
            ->count();

        if ($existingCount >= $slotCapacity) {
            return response()->json(['message' => 'This time slot is already full. Please choose another.'], 422);
        }

        $appointment = Appointment::create([
            'date' => $request->date,
            'time' => sprintf('%s:00', $request->time),
            'service_id' => $request->service_id,
            'size_id' => $request->size_id,
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
            'notes' => $request->notes,
            'status' => 'scheduled',
        ]);

        $appointment->load(['service', 'size']);

        Notification::notifyAdmins(
            'appointment_created',
            'New Appointment Booked',
            $appointment->customer_name . ' booked a ' . ($appointment->service->name ?? 'service') . ' for ' . \Carbon\Carbon::parse($appointment->date)->format('M d, Y') . ' at ' . \Carbon\Carbon::parse($appointment->time)->format('g:i A') . '.',
            'fa-solid fa-calendar-plus',
            'primary',
            '/panel/appointments',
            ['appointment_id' => $appointment->id]
        );

        return response()->json(['message' => 'Appointment created successfully.', 'appointment' => $appointment]);
    }

    /**
     * Update an existing appointment (AJAX)
     */
    public function updateAppointment(Request $request, Appointment $appointment)
    {
        $request->validate([
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'service_id' => 'required|exists:services,id',
            'size_id' => 'required|exists:sizes,id',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        // Validate time is within operating hours
        $openingTime = Setting::get('opening_time', '07:00');
        $closingTime = Setting::get('closing_time', '17:00');

        if ($request->time < $openingTime || $request->time >= $closingTime) {
            return response()->json(['message' => 'Selected time is outside operating hours (' . $openingTime . ' – ' . $closingTime . ').'], 422);
        }

        // Validate slot capacity (exclude this appointment from the count)
        $slotCapacity = (int) Setting::get('slot_capacity', 2);
        $existingCount = Appointment::where('date', $request->date)
            ->where('time', sprintf('%s:00', $request->time))
            ->whereNotIn('status', ['cancelled'])
            ->where('id', '!=', $appointment->id)
            ->count();

        if ($existingCount >= $slotCapacity) {
            return response()->json(['message' => 'This time slot is already full. Please choose another.'], 422);
        }

        $appointment->update([
            'date' => $request->date,
            'time' => sprintf('%s:00', $request->time),
            'service_id' => $request->service_id,
            'size_id' => $request->size_id,
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
            'notes' => $request->notes,
        ]);

        $appointment->load(['service', 'size']);

        return response()->json(['message' => 'Appointment updated successfully.', 'appointment' => $appointment]);
    }

    /**
     * Update appointment status (AJAX)
     */
    public function updateAppointmentStatus(Request $request, Appointment $appointment)
    {
        $request->validate([
            'status' => 'required|in:scheduled,in_progress,completed,cancelled',
        ]);

        $data = ['status' => $request->status];

        if ($request->status === 'completed' && $appointment->status !== 'completed') {
            $basePrice  = $appointment->service?->price ?? 0;
            $multiplier = $appointment->size?->multiplier ?? 1;
            $data['amount'] = round($basePrice * $multiplier, 2);
            $data['completed_at'] = now();
        }

        if ($request->status !== 'completed' && $appointment->status === 'completed') {
            $data['amount'] = null;
            $data['completed_at'] = null;
        }

        $appointment->update($data);

        // Send notification for status changes
        $statusLabels = [
            'completed' => ['Appointment Completed', 'fa-solid fa-circle-check', 'success'],
            'cancelled' => ['Appointment Cancelled', 'fa-solid fa-circle-xmark', 'danger'],
            'in_progress' => ['Appointment In Progress', 'fa-solid fa-spinner', 'warning'],
            'scheduled' => ['Appointment Rescheduled', 'fa-solid fa-calendar-check', 'info'],
        ];

        $label = $statusLabels[$request->status] ?? ['Status Updated', 'fa-solid fa-circle-info', 'secondary'];

        Notification::notifyAdmins(
            'appointment_' . $request->status,
            $label[0],
            $appointment->customer_name . '\'s appointment has been marked as ' . str_replace('_', ' ', $request->status) . '.',
            $label[1],
            $label[2],
            '/panel/appointments',
            ['appointment_id' => $appointment->id]
        );

        return response()->json(['message' => 'Status updated successfully.', 'appointment' => $appointment->fresh()->load(['service', 'size'])]);
    }

    /**
     * Delete appointment (AJAX)
     */
    public function destroyAppointment(Appointment $appointment)
    {
        $appointment->delete();
        return response()->json(['message' => 'Appointment deleted successfully.']);
    }

    /**
     * Services & sizes data
     */
    public function services()
    {
        return response()->json([
            'services' => Service::orderBy('name')->get(),
            'sizes' => Size::orderBy('name')->get(),
        ]);
    }

    /**
     * Store service (AJAX)
     */
    public function storeService(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
        ]);

        $service = Service::create($request->only('name', 'description', 'price'));
        return response()->json(['message' => 'Service created successfully.', 'service' => $service]);
    }

    /**
     * Update service (AJAX)
     */
    public function updateService(Request $request, Service $service)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
        ]);

        $service->update($request->only('name', 'description', 'price'));
        return response()->json(['message' => 'Service updated successfully.', 'service' => $service]);
    }

    /**
     * Delete service (AJAX)
     */
    public function destroyService(Service $service)
    {
        // Check if service is used by any appointments
        $count = Appointment::where('service_id', $service->id)->count();
        if ($count > 0) {
            return response()->json([
                'message' => "Cannot delete this service — it is linked to {$count} appointment(s). Remove or reassign those appointments first.",
            ], 422);
        }

        $service->delete();
        return response()->json(['message' => 'Service deleted successfully.']);
    }

    /**
     * Store size (AJAX)
     */
    public function storeSize(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'multiplier' => 'nullable|numeric|min:0.01|max:99.99',
        ]);

        $size = Size::create($request->only('name', 'description', 'multiplier'));
        return response()->json(['message' => 'Size created successfully.', 'size' => $size]);
    }

    /**
     * Update size (AJAX)
     */
    public function updateSize(Request $request, Size $size)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'multiplier' => 'nullable|numeric|min:0.01|max:99.99',
        ]);

        $size->update($request->only('name', 'description', 'multiplier'));
        return response()->json(['message' => 'Size updated successfully.', 'size' => $size]);
    }

    /**
     * Delete size (AJAX)
     */
    public function destroySize(Size $size)
    {
        // Check if size is used by any appointments
        $count = Appointment::where('size_id', $size->id)->count();
        if ($count > 0) {
            return response()->json([
                'message' => "Cannot delete this size — it is linked to {$count} appointment(s). Remove or reassign those appointments first.",
            ], 422);
        }

        $size->delete();
        return response()->json(['message' => 'Size deleted successfully.']);
    }

    /**
     * Sales report data
     */
    public function salesReport(Request $request)
    {
        $period = $request->input('period', 'month');
        $endDate = now()->endOfDay();

        switch ($period) {
            case 'today':
                $startDate = now()->startOfDay();
                break;
            case 'week':
                $startDate = now()->startOfWeek();
                break;
            case 'month':
                $startDate = now()->startOfMonth();
                break;
            case 'year':
                $startDate = now()->startOfYear();
                break;
            case 'custom':
                $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date)->startOfDay() : now()->startOfMonth();
                $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date)->endOfDay() : now()->endOfDay();
                break;
            default:
                $startDate = now()->startOfMonth();
        }

        $salesQuery = Appointment::completed()
            ->with(['service', 'size'])
            ->whereBetween('completed_at', [$startDate, $endDate]);

        if ($request->filled('service_id') && $request->service_id !== 'all') {
            $salesQuery->where('service_id', $request->service_id);
        }

        $sales = $salesQuery->orderBy('completed_at', 'desc')->get();

        $totalRevenue = $sales->sum('amount');
        $totalTransactions = $sales->count();
        $averageTicket = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;

        // Previous period
        $periodDuration = $startDate->diffInDays($endDate) ?: 1;
        $prevStart = (clone $startDate)->subDays($periodDuration);
        $prevEnd = (clone $startDate)->subSecond();

        $prevRevenue = Appointment::completed()->whereBetween('completed_at', [$prevStart, $prevEnd])->sum('amount');
        $prevTransactions = Appointment::completed()->whereBetween('completed_at', [$prevStart, $prevEnd])->count();

        $revenueChange = $prevRevenue > 0
            ? round((($totalRevenue - $prevRevenue) / $prevRevenue) * 100, 1)
            : ($totalRevenue > 0 ? 100 : 0);
        $transactionsChange = $prevTransactions > 0
            ? round((($totalTransactions - $prevTransactions) / $prevTransactions) * 100, 1)
            : ($totalTransactions > 0 ? 100 : 0);

        // Revenue by service
        $revenueByService = Appointment::completed()
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->selectRaw('service_id, SUM(amount) as total_revenue, COUNT(*) as total_count')
            ->groupBy('service_id')
            ->with('service')
            ->orderByDesc('total_revenue')
            ->get()
            ->map(fn($item) => [
                'service_name' => $item->service->name ?? 'Unknown',
                'total_revenue' => (float) $item->total_revenue,
                'total_count' => (int) $item->total_count,
                'percentage' => $totalRevenue > 0 ? round(($item->total_revenue / $totalRevenue) * 100, 1) : 0,
            ]);

        // Chart data
        $chartStart = max($startDate->timestamp, now()->subDays(29)->startOfDay()->timestamp);
        $chartStartDate = Carbon::createFromTimestamp($chartStart);

        $dailyRevenue = Appointment::completed()
            ->whereBetween('completed_at', [$chartStartDate, $endDate])
            ->selectRaw('DATE(completed_at) as sale_date, SUM(amount) as daily_total, COUNT(*) as daily_count')
            ->groupBy('sale_date')
            ->orderBy('sale_date')
            ->get()
            ->keyBy('sale_date');

        $chartData = [];
        $current = (clone $chartStartDate);
        while ($current <= $endDate) {
            $dateKey = $current->format('Y-m-d');
            $chartData[] = [
                'date' => $current->format('M d'),
                'revenue' => (float) ($dailyRevenue[$dateKey]->daily_total ?? 0),
                'count' => (int) ($dailyRevenue[$dateKey]->daily_count ?? 0),
            ];
            $current->addDay();
        }

        // Top customers
        $topCustomers = Appointment::completed()
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->selectRaw('customer_name, customer_email, customer_phone, SUM(amount) as total_spent, COUNT(*) as visit_count')
            ->groupBy('customer_name', 'customer_email', 'customer_phone')
            ->orderByDesc('total_spent')
            ->limit(10)
            ->get();

        $services = Service::orderBy('name')->get();

        return response()->json([
            'sales' => $sales,
            'totalRevenue' => (float) $totalRevenue,
            'totalTransactions' => $totalTransactions,
            'averageTicket' => round((float) $averageTicket, 2),
            'revenueChange' => $revenueChange,
            'transactionsChange' => $transactionsChange,
            'revenueByService' => $revenueByService,
            'chartData' => $chartData,
            'topCustomers' => $topCustomers,
            'services' => $services,
            'period' => $period,
            'startDate' => $startDate->format('Y-m-d'),
            'endDate' => $endDate->format('Y-m-d'),
            'startDateFormatted' => $startDate->format('M d'),
            'endDateFormatted' => $endDate->format('M d'),
        ]);
    }

    // ───────────────────────────────────────────────
    //  INVENTORY
    // ───────────────────────────────────────────────

    /**
     * Inventory page data — items, categories, stats
     */
    public function inventory(Request $request)
    {
        $query = InventoryItem::with('category');

        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $items = $query->orderBy('name')->paginate(15)->withQueryString();

        $categories = InventoryCategory::withCount('items')->orderBy('name')->get();

        $stats = [
            'totalItems'   => InventoryItem::count(),
            'totalValue'   => (float) InventoryItem::selectRaw('SUM(cost * quantity) as val')->value('val') ?? 0,
            'lowStock'     => InventoryItem::where('status', 'low_stock')->count(),
            'outOfStock'   => InventoryItem::where('status', 'out_of_stock')->count(),
        ];

        return response()->json([
            'items'      => $items,
            'categories' => $categories,
            'stats'      => $stats,
        ]);
    }

    /**
     * Store a new inventory category
     */
    public function storeInventoryCategory(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $category = InventoryCategory::create($request->only('name', 'description'));

        return response()->json(['message' => 'Category created successfully.', 'category' => $category]);
    }

    /**
     * Update an inventory category
     */
    public function updateInventoryCategory(Request $request, InventoryCategory $inventoryCategory)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $inventoryCategory->update($request->only('name', 'description'));

        return response()->json(['message' => 'Category updated successfully.', 'category' => $inventoryCategory]);
    }

    /**
     * Delete an inventory category
     */
    public function destroyInventoryCategory(InventoryCategory $inventoryCategory)
    {
        if ($inventoryCategory->items()->count() > 0) {
            return response()->json(['message' => 'Cannot delete category with existing items. Move or delete items first.'], 422);
        }

        $inventoryCategory->delete();
        return response()->json(['message' => 'Category deleted successfully.']);
    }

    /**
     * Store a new inventory item
     */
    public function storeInventoryItem(Request $request)
    {
        $request->validate([
            'category_id'   => 'required|exists:inventory_categories,id',
            'name'          => 'required|string|max:255',
            'description'   => 'nullable|string',
            'sku'           => 'nullable|string|max:255|unique:inventory_items,sku',
            'unit'          => 'required|string|max:50',
            'cost'          => 'required|numeric|min:0',
            'quantity'      => 'required|integer|min:0',
            'reorder_level' => 'required|integer|min:0',
        ]);

        $item = InventoryItem::create($request->only(
            'category_id', 'name', 'description', 'sku',
            'unit', 'cost', 'quantity', 'reorder_level'
        ));

        $item->refreshStatus();

        // Log initial stock
        if ($item->quantity > 0) {
            InventoryLog::create([
                'item_id'         => $item->id,
                'user_id'         => auth()->id(),
                'type'            => 'stock_in',
                'quantity'        => $item->quantity,
                'quantity_before' => 0,
                'quantity_after'  => $item->quantity,
                'notes'           => 'Initial stock on item creation.',
            ]);
        }

        return response()->json(['message' => 'Item created successfully.', 'item' => $item->load('category')]);
    }

    /**
     * Update an inventory item (details only — stock changes via adjustStock)
     */
    public function updateInventoryItem(Request $request, InventoryItem $inventoryItem)
    {
        $request->validate([
            'category_id'   => 'required|exists:inventory_categories,id',
            'name'          => 'required|string|max:255',
            'description'   => 'nullable|string',
            'sku'           => 'nullable|string|max:255|unique:inventory_items,sku,' . $inventoryItem->id,
            'unit'          => 'required|string|max:50',
            'cost'          => 'required|numeric|min:0',
            'reorder_level' => 'required|integer|min:0',
        ]);

        $inventoryItem->update($request->only(
            'category_id', 'name', 'description', 'sku',
            'unit', 'cost', 'reorder_level'
        ));

        $inventoryItem->refreshStatus();

        return response()->json(['message' => 'Item updated successfully.', 'item' => $inventoryItem->load('category')]);
    }

    /**
     * Delete an inventory item
     */
    public function destroyInventoryItem(InventoryItem $inventoryItem)
    {
        $inventoryItem->delete();
        return response()->json(['message' => 'Item deleted successfully.']);
    }

    /**
     * Adjust stock (stock in / stock out / manual adjustment)
     */
    public function adjustStock(Request $request, InventoryItem $inventoryItem)
    {
        $request->validate([
            'type'     => 'required|in:stock_in,stock_out,adjustment',
            'quantity' => 'required|integer|min:1',
            'notes'    => 'nullable|string',
        ]);

        $before = $inventoryItem->quantity;

        if ($request->type === 'stock_in') {
            $inventoryItem->quantity += $request->quantity;
        } elseif ($request->type === 'stock_out') {
            if ($request->quantity > $inventoryItem->quantity) {
                return response()->json(['message' => 'Insufficient stock. Current quantity is ' . $inventoryItem->quantity . '.'], 422);
            }
            $inventoryItem->quantity -= $request->quantity;
        } else {
            // adjustment — set to exact value
            $inventoryItem->quantity = $request->quantity;
        }

        $inventoryItem->save();
        $inventoryItem->refreshStatus();

        InventoryLog::create([
            'item_id'         => $inventoryItem->id,
            'user_id'         => auth()->id(),
            'type'            => $request->type,
            'quantity'        => $request->type === 'stock_out' ? -$request->quantity : $request->quantity,
            'quantity_before' => $before,
            'quantity_after'  => $inventoryItem->quantity,
            'notes'           => $request->notes,
        ]);

        // Notify admins if stock is now low or out
        if ($inventoryItem->status === 'low_stock') {
            Notification::notifyAdmins(
                'low_stock',
                'Low Stock Alert',
                $inventoryItem->name . ' is running low — only ' . $inventoryItem->quantity . ' ' . $inventoryItem->unit . ' remaining.',
                'fa-solid fa-triangle-exclamation',
                'warning',
                '/panel/inventory',
                ['item_id' => $inventoryItem->id]
            );
        } elseif ($inventoryItem->status === 'out_of_stock') {
            Notification::notifyAdmins(
                'out_of_stock',
                'Out of Stock',
                $inventoryItem->name . ' is now out of stock!',
                'fa-solid fa-box-open',
                'danger',
                '/panel/inventory',
                ['item_id' => $inventoryItem->id]
            );
        }

        return response()->json(['message' => 'Stock updated successfully.', 'item' => $inventoryItem->load('category')]);
    }

    /**
     * Inventory activity log for a specific item (or all items)
     */
    public function inventoryLogs(Request $request)
    {
        $query = InventoryLog::with(['item', 'user'])->orderBy('created_at', 'desc');

        if ($request->filled('item_id')) {
            $query->where('item_id', $request->item_id);
        }

        $logs = $query->paginate(20)->withQueryString();

        return response()->json(['logs' => $logs]);
    }

    /* ================================================================
     *  NOTIFICATIONS
     * ================================================================ */

    /**
     * List notifications for the authenticated user
     */
    public function notifications(Request $request)
    {
        $query = Notification::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc');

        if ($request->filter === 'unread') {
            $query->unread();
        } elseif ($request->filter === 'read') {
            $query->read();
        }

        $notifications = $query->paginate(20)->withQueryString();

        $stats = [
            'total'  => Notification::where('user_id', auth()->id())->count(),
            'unread' => Notification::where('user_id', auth()->id())->unread()->count(),
        ];

        return response()->json([
            'notifications' => $notifications,
            'stats' => $stats,
        ]);
    }

    /**
     * Unread count (used by the bell icon for real-time polling)
     */
    public function notificationsUnreadCount()
    {
        return response()->json([
            'count' => Notification::where('user_id', auth()->id())->unread()->count(),
            'recent' => Notification::where('user_id', auth()->id())
                ->unread()
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get(),
        ]);
    }

    /**
     * Mark a single notification as read
     */
    public function markNotificationRead(Notification $notification)
    {
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }

        $notification->markAsRead();

        return response()->json(['message' => 'Notification marked as read.']);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsRead()
    {
        Notification::where('user_id', auth()->id())
            ->unread()
            ->update(['read_at' => now()]);

        return response()->json(['message' => 'All notifications marked as read.']);
    }

    /**
     * Delete a single notification
     */
    public function destroyNotification(Notification $notification)
    {
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }

        $notification->delete();

        return response()->json(['message' => 'Notification deleted.']);
    }

    /**
     * Clear all read notifications
     */
    public function clearReadNotifications()
    {
        Notification::where('user_id', auth()->id())
            ->read()
            ->delete();

        return response()->json(['message' => 'Read notifications cleared.']);
    }

    /* ================================================================
     *  SETTINGS
     * ================================================================ */

    /**
     * Get settings data (profile + business settings)
     */
    public function settings()
    {
        $user = auth()->user();

        $data = [
            'profile' => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
            ],
            'role' => $user->isAdmin() ? 'admin' : 'staff',
        ];

        // Only admins get business settings
        if ($user->isAdmin()) {
            $data['business'] = Setting::allAsArray();
        }

        return response()->json($data);
    }

    /**
     * Update profile (name & email)
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->update($request->only('name', 'email'));

        return response()->json(['message' => 'Profile updated successfully.']);
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|string|min:8|confirmed',
        ]);

        $user = auth()->user();

        if (!password_verify($request->current_password, $user->password)) {
            return response()->json(['message' => 'Current password is incorrect.'], 422);
        }

        $user->update(['password' => bcrypt($request->password)]);

        return response()->json(['message' => 'Password changed successfully.']);
    }

    /**
     * Update business settings (admin only — route-level middleware enforces this)
     */
    public function updateBusinessSettings(Request $request)
    {
        $request->validate([
            'business_name'    => 'required|string|max:255',
            'business_email'   => 'required|email|max:255',
            'business_phone'   => 'required|string|max:50',
            'business_address' => 'required|string|max:500',
            'opening_time'     => 'required|date_format:H:i',
            'closing_time'     => 'required|date_format:H:i',
            'slot_duration'    => 'required|integer|min:10|max:120',
            'slot_capacity'    => 'required|integer|min:1|max:20',
            'currency'         => 'required|string|max:10',
            'timezone'         => 'required|string|max:100',
            'show_emergency_phone' => 'nullable|in:0,1',
        ]);

        $keys = [
            'business_name', 'business_email', 'business_phone', 'business_address',
            'opening_time', 'closing_time', 'slot_duration', 'slot_capacity',
            'currency', 'timezone', 'show_emergency_phone',
        ];

        foreach ($keys as $key) {
            if ($request->has($key)) {
                Setting::set($key, $request->input($key));
            }
        }

        return response()->json(['message' => 'Business settings updated successfully.']);
    }

    /* ================================================================
     *  USERS MANAGEMENT (Admin only)
     * ================================================================ */

    /**
     * List all users with roles
     */
    public function users(Request $request)
    {
        $query = User::with('role');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role') && $request->role !== 'all') {
            $query->where('role_id', $request->role);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        $roles = Role::orderBy('id')->get();

        $stats = [
            'total'  => User::count(),
            'admins' => User::where('role_id', 2)->count(),
            'staff'  => User::where('role_id', 3)->count(),
        ];

        return response()->json([
            'users' => $users,
            'roles' => $roles,
            'stats' => $stats,
        ]);
    }

    /**
     * Create a new user
     */
    public function storeUser(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'role_id'  => 'required|exists:roles,id',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($request->password),
            'role_id'  => $request->role_id,
        ]);

        return response()->json(['message' => 'User created successfully.', 'user' => $user->load('role')]);
    }

    /**
     * Update a user
     */
    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email,' . $user->id,
            'role_id'  => 'required|exists:roles,id',
            'password' => 'nullable|string|min:8',
        ]);

        $data = $request->only('name', 'email', 'role_id');

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

        return response()->json(['message' => 'User updated successfully.', 'user' => $user->load('role')]);
    }

    /**
     * Delete a user
     */
    public function destroyUser(User $user)
    {
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'You cannot delete your own account.'], 422);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully.']);
    }

    // ───────────────────────────────────────────────
    //  EXPORT DAILY REPORT (CSV)
    // ───────────────────────────────────────────────

    public function exportDailyReport()
    {
        $today = today();
        $todayLabel = $today->format('M d, Y');
        $filename = 'jnj-daily-report-' . $today->format('Y-m-d') . '.csv';

        // ── Sales data ──
        $sales = Appointment::completed()
            ->with(['service', 'size'])
            ->whereDate('completed_at', $today)
            ->orderBy('completed_at')
            ->get();

        $todayRevenue = $sales->sum('amount');
        $todayTransactions = $sales->count();

        // ── All appointments today ──
        $appointments = Appointment::with(['service', 'size'])
            ->whereDate('date', $today)
            ->orderBy('time')
            ->get();

        $scheduled   = $appointments->where('status', 'scheduled')->count();
        $inProgress  = $appointments->where('status', 'in_progress')->count();
        $completed   = $appointments->where('status', 'completed')->count();
        $cancelled   = $appointments->where('status', 'cancelled')->count();

        // ── Inventory data ──
        $inventoryItems = InventoryItem::with('category')->orderBy('name')->get();
        $totalInventoryValue = $inventoryItems->sum(fn($i) => $i->cost * $i->quantity);
        $lowStock    = $inventoryItems->where('status', 'low_stock')->count();
        $outOfStock  = $inventoryItems->where('status', 'out_of_stock')->count();

        // ── Build CSV ──
        $lines = [];

        // Header
        $lines[] = 'JNJ Auto Car Wash - Daily Report';
        $lines[] = 'Generated: ' . now()->format('M d, Y h:i A');
        $lines[] = 'Report Date: ' . $todayLabel;
        $lines[] = '';

        // Summary
        $lines[] = '=== DAILY SUMMARY ===';
        $lines[] = 'Total Revenue,"' . number_format($todayRevenue, 2) . '"';
        $lines[] = 'Completed Transactions,' . $todayTransactions;
        $lines[] = 'Average Ticket,"' . ($todayTransactions > 0 ? number_format($todayRevenue / $todayTransactions, 2) : '0.00') . '"';
        $lines[] = '';
        $lines[] = 'Scheduled,' . $scheduled;
        $lines[] = 'In Progress,' . $inProgress;
        $lines[] = 'Completed,' . $completed;
        $lines[] = 'Cancelled,' . $cancelled;
        $lines[] = 'Total Appointments,' . $appointments->count();
        $lines[] = '';

        // Sales transactions
        $lines[] = '=== SALES TRANSACTIONS ===';
        $lines[] = '#,Customer,Email,Phone,Service,Size,Time,Completed At,Amount';
        foreach ($sales as $sale) {
            $lines[] = implode(',', [
                $sale->id,
                '"' . str_replace('"', '""', $sale->customer_name ?? '') . '"',
                '"' . str_replace('"', '""', $sale->customer_email ?? '') . '"',
                '"' . str_replace('"', '""', $sale->customer_phone ?? '') . '"',
                '"' . str_replace('"', '""', $sale->service->name ?? '—') . '"',
                '"' . str_replace('"', '""', $sale->size->name ?? '—') . '"',
                $sale->time ?? '',
                $sale->completed_at ? Carbon::parse($sale->completed_at)->format('h:i A') : '',
                $sale->amount ?? 0,
            ]);
        }
        $lines[] = '';

        // Inventory
        $lines[] = '=== CURRENT INVENTORY ===';
        $lines[] = 'Total Items,' . $inventoryItems->count();
        $lines[] = 'Total Inventory Value,"' . number_format($totalInventoryValue, 2) . '"';
        $lines[] = 'Low Stock Items,' . $lowStock;
        $lines[] = 'Out of Stock Items,' . $outOfStock;
        $lines[] = '';
        $lines[] = 'Item,Category,SKU,Quantity,Unit,Cost,Value,Reorder Level,Status';
        foreach ($inventoryItems as $item) {
            $value = $item->cost * $item->quantity;
            $statusLabel = match($item->status) {
                'in_stock'     => 'In Stock',
                'low_stock'    => 'Low Stock',
                'out_of_stock' => 'Out of Stock',
                default        => $item->status,
            };
            $lines[] = implode(',', [
                '"' . str_replace('"', '""', $item->name) . '"',
                '"' . str_replace('"', '""', $item->category->name ?? '—') . '"',
                '"' . str_replace('"', '""', $item->sku ?? '') . '"',
                $item->quantity,
                '"' . ($item->unit ?? 'pcs') . '"',
                $item->cost ?? 0,
                '"' . number_format($value, 2) . '"',
                $item->reorder_level ?? 0,
                $statusLabel,
            ]);
        }

        $csv = implode("\n", $lines);

        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
