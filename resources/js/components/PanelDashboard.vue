<template>
    <div>
        <overlay :show="loading">
            <!-- ── Hero + Stats Row ── -->
            <section class="row g-4 mb-4">
                <!-- Hero card -->
                <div class="col-12 col-xl-5">
                    <div class="hero-card rounded-4 p-4 h-100 text-white position-relative overflow-hidden d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-inline-flex align-items-center gap-2 hero-badge rounded-pill px-3 py-2 mb-3">
                                <span class="pulse-dot bg-white rounded-circle d-inline-block"></span>
                                <span class="fw-bold small">Live Operations</span>
                            </div>
                            <h3 class="hero-title fw-bold mb-2">Welcome back, Admin</h3>
                            <p class="mb-0 text-white-50 hero-copy small">Take full control of your operations with a real-time overview of bookings, revenue streams, and inventory status. Make faster decisions, reduce downtime, and ensure every aspect of your car wash business is performing at its best.</p>
                        </div>
                        <div class="d-flex flex-wrap gap-2 mt-4">
                            <a href="/panel/appointments" class="btn btn-light rounded-4 px-3 py-2 fw-semibold text-info-emphasis btn-sm">
                                <i class="fa-solid fa-calendar-plus me-1"></i>
                                View Schedule
                            </a>
                            <a href="/panel/sales" class="btn btn-outline-light rounded-4 px-3 py-2 fw-semibold btn-sm">
                                <i class="fa-solid fa-chart-line me-1"></i>
                                Sales Analytics
                            </a>
                        </div>
                        <span class="hero-orb orb-1"></span>
                        <span class="hero-orb orb-2"></span>
                    </div>
                </div>

                <!-- Stats grid -->
                <div class="col-12 col-xl-7">
                    <div class="row g-3 h-100">
                        <div class="col-6 col-md-3 col-xl-6">
                            <div class="panel-card rounded-4 p-4 h-100 dash-stat-card">
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <span class="stat-icon rounded-4 d-inline-flex align-items-center justify-content-center bg-success-subtle text-success">
                                        <i class="fa-solid fa-peso-sign"></i>
                                    </span>
                                    <span class="small text-secondary fw-semibold">Today's Revenue</span>
                                </div>
                                <div class="h2 fw-bold mb-1">₱{{ formatNumber(data.todayRevenue) }}</div>
                                <div class="small fw-bold" :class="data.revenueChange >= 0 ? 'text-success' : 'text-danger'">
                                    <i class="fa-solid me-1" :class="data.revenueChange >= 0 ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down'"></i>
                                    {{ Math.abs(data.revenueChange) }}% vs yesterday
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 col-xl-6">
                            <div class="panel-card rounded-4 p-4 h-100 dash-stat-card">
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <span class="stat-icon rounded-4 d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary">
                                        <i class="fa-solid fa-calendar-check"></i>
                                    </span>
                                    <span class="small text-secondary fw-semibold">Booked Slots</span>
                                </div>
                                <div class="h2 fw-bold mb-1">
                                    {{ data.bookedSlots }}
                                    <span class="h5 fw-normal text-secondary">/ {{ data.totalSlots }}</span>
                                </div>
                                <div class="progress rounded-pill mt-1" style="height: 6px">
                                    <div class="progress-bar progress-custom rounded-pill" :style="{ width: (data.totalSlots > 0 ? (data.bookedSlots / data.totalSlots) * 100 : 0) + '%' }"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 col-xl-6">
                            <div class="panel-card rounded-4 p-4 h-100 dash-stat-card">
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <span class="stat-icon rounded-4 d-inline-flex align-items-center justify-content-center bg-warning-subtle text-warning">
                                        <i class="fa-solid fa-spinner"></i>
                                    </span>
                                    <span class="small text-secondary fw-semibold">In Progress</span>
                                </div>
                                <div class="h2 fw-bold mb-1">{{ String(data.inProgressCount).padStart(2, "0") }}</div>
                                <div class="small text-warning fw-bold">
                                    <i class="fa-solid fa-clock me-1"></i>
                                    Currently washing
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 col-xl-6">
                            <div class="panel-card rounded-4 p-4 h-100 dash-stat-card">
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <span class="stat-icon rounded-4 d-inline-flex align-items-center justify-content-center bg-info-subtle text-info">
                                        <i class="fa-solid fa-circle-check"></i>
                                    </span>
                                    <span class="small text-secondary fw-semibold">Completed</span>
                                </div>
                                <div class="h2 fw-bold mb-1">{{ String(data.completedToday).padStart(2, "0") }}</div>
                                <div class="small text-success fw-bold">
                                    <i class="fa-solid fa-check-double me-1"></i>
                                    Done today
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ── Revenue Sparkline + Top Services ── -->
            <section class="row g-4 mb-4">
                <!-- Weekly revenue chart -->
                <div class="col-12 col-xl-8">
                    <div class="panel-card rounded-4 p-4 h-100">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                            <div>
                                <h3 class="h5 fw-bold mb-1">
                                    <i class="fa-solid fa-chart-area text-info me-2"></i>
                                    Weekly Revenue
                                </h3>
                                <p class="small text-secondary mb-0">Last 7 days earnings overview</p>
                            </div>
                            <div class="text-end">
                                <div class="h4 fw-bold mb-0 text-info-emphasis">₱{{ formatNumber(weeklyTotal) }}</div>
                                <div class="small text-secondary">7-day total</div>
                            </div>
                        </div>

                        <!-- Simple bar chart -->
                        <div class="d-flex align-items-end gap-2 justify-content-between h-100 py-5">
                            <div v-for="(day, i) in data.weeklyRevenue" :key="i" class="flex-fill text-center d-flex flex-column justify-content-end align-items-center h-100 py-5">
                                <div class="small fw-bold mb-1" v-if="day.total > 0">₱{{ formatCompact(day.total) }}</div>
                                <div class="revenue-bar rounded-top-4 w-100" :style="{ height: barHeight(day.total) + '%', minHeight: '4px' }" :class="i === data.weeklyRevenue.length - 1 ? 'bar-today' : 'bar-default'" :title="day.date + ': ₱' + formatNumber(day.total)"></div>
                                <div class="small text-secondary mt-2 fw-semibold">{{ day.day }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top services -->
                <div class="col-12 col-xl-4">
                    <div class="panel-card rounded-4 p-4 h-100">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h3 class="h5 fw-bold mb-1">
                                    <i class="fa-solid fa-trophy text-warning me-2"></i>
                                    Top Services
                                </h3>
                                <p class="small text-secondary mb-0">Today's best sellers</p>
                            </div>
                        </div>

                        <div v-if="data.topServices.length > 0" class="d-flex flex-column gap-3">
                            <div v-for="(svc, idx) in data.topServices" :key="svc.name" class="d-flex align-items-center gap-3 p-3 rounded-4 service-item">
                                <div class="rank-badge rounded-circle d-flex align-items-center justify-content-center fw-bold" :class="idx === 0 ? 'rank-gold' : idx === 1 ? 'rank-silver' : 'rank-bronze'">
                                    {{ idx + 1 }}
                                </div>
                                <div class="flex-fill">
                                    <div class="fw-bold small">{{ svc.name }}</div>
                                    <div class="small text-secondary">{{ svc.bookings }} booking{{ svc.bookings !== 1 ? "s" : "" }}</div>
                                </div>
                                <div class="fw-bold text-info-emphasis">₱{{ formatNumber(svc.revenue) }}</div>
                            </div>
                        </div>

                        <div v-else class="text-center py-5">
                            <i class="fa-solid fa-trophy fa-2x text-secondary mb-2 d-block opacity-25"></i>
                            <div class="small text-secondary">No completed services yet today.</div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ── Recent Appointments + Time Slots ── -->
            <section class="row g-4 mb-4">
                <!-- Appointments table -->
                <div class="col-12 col-xl-8">
                    <div class="panel-card rounded-4 p-4 h-100">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
                            <div>
                                <h3 class="h5 fw-bold mb-1">
                                    <i class="fa-solid fa-clock-rotate-left text-primary me-2"></i>
                                    Recent Appointments
                                </h3>
                                <p class="small text-secondary mb-0">Today's booking activity</p>
                            </div>
                            <a href="/panel/appointments" class="btn btn-sm btn-light border rounded-pill px-3 fw-semibold align-self-start align-self-md-center">
                                View all
                                <i class="fa-solid fa-arrow-right ms-1 small"></i>
                            </a>
                        </div>

                        <!-- Desktop table -->
                        <div class="table-responsive d-none d-md-block">
                            <table class="table align-middle mb-0 admin-table">
                                <thead>
                                    <tr>
                                        <th>Customer</th>
                                        <th>Service</th>
                                        <th>Size</th>
                                        <th>Time</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="a in data.recentAppointments" :key="a.id">
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="avatar-circle rounded-circle d-inline-flex align-items-center justify-content-center fw-bold small">
                                                    {{ a.customer_name.charAt(0).toUpperCase() }}
                                                </span>
                                                <div>
                                                    <div class="fw-bold">{{ a.customer_name }}</div>
                                                    <div class="small text-secondary" v-if="a.customer_phone">{{ a.customer_phone }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ a.service }}</td>
                                        <td>{{ a.size }}</td>
                                        <td class="fw-semibold">{{ a.time }}</td>
                                        <td>
                                            <span class="badge rounded-pill px-3 py-2" :class="statusClass(a.status)">
                                                <span class="badge-dot"></span>
                                                {{ statusLabel(a.status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr v-if="data.recentAppointments.length === 0">
                                        <td colspan="5" class="text-center py-5 text-secondary">
                                            <i class="fa-solid fa-calendar-xmark fa-2x mb-2 d-block opacity-25"></i>
                                            No appointments scheduled today.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Mobile cards -->
                        <div class="d-block d-md-none">
                            <div class="d-flex flex-column gap-3">
                                <div v-for="a in data.recentAppointments" :key="'m-' + a.id" class="border rounded-4 p-3 d-flex align-items-center gap-3">
                                    <span class="avatar-circle rounded-circle d-inline-flex align-items-center justify-content-center fw-bold small flex-shrink-0">
                                        {{ a.customer_name.charAt(0).toUpperCase() }}
                                    </span>
                                    <div class="flex-fill">
                                        <div class="fw-bold small">{{ a.customer_name }}</div>
                                        <div class="small text-secondary">{{ a.service }} • {{ a.time }}</div>
                                    </div>
                                    <span class="badge rounded-pill px-2 py-1" :class="statusClass(a.status)">
                                        <span class="badge-dot"></span>
                                        {{ statusLabel(a.status) }}
                                    </span>
                                </div>
                                <div v-if="data.recentAppointments.length === 0" class="text-center py-4 text-secondary small">No appointments today.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Time slots -->
                <div class="col-12 col-xl-4">
                    <div class="panel-card rounded-4 p-4 h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h3 class="h5 fw-bold mb-1">
                                    <i class="fa-solid fa-table-cells text-info me-2"></i>
                                    Time Slots
                                </h3>
                                <p class="small text-secondary mb-0">Today's availability</p>
                            </div>
                            <span class="badge text-bg-info rounded-pill px-3 py-2 small">
                                <i class="fa-solid fa-circle fa-2xs me-1 pulse-dot-inline"></i>
                                Live
                            </span>
                        </div>

                        <div class="slot-grid" style="max-height: 570px; overflow-y: auto; overflow-x: hidden">
                            <div class="row g-2">
                                <div class="col-6" v-for="(slot, i) in data.slots" :key="i">
                                    <div class="slot-card rounded-4 p-3 h-100 text-center" :class="'slot-' + slot.status">
                                        <div class="fw-bold small mb-1">{{ slot.time }}</div>
                                        <div class="small text-secondary">{{ slot.available }} / {{ slot.capacity }}</div>
                                        <span
                                            class="badge rounded-pill mt-1 small"
                                            :class="{
                                                'text-bg-success': slot.status === 'open',
                                                'text-bg-warning': slot.status === 'partial',
                                                'text-bg-danger': slot.status === 'full',
                                            }">
                                            {{ slot.status === "open" ? "Open" : slot.status === "partial" ? "Partial" : "Full" }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ── Inventory Alerts ── -->
            <section v-if="data.inventoryAlerts && data.inventoryAlerts.length > 0" class="row g-4">
                <div class="col-12">
                    <div class="panel-card rounded-4 p-4">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
                            <div>
                                <h3 class="h5 fw-bold mb-1">
                                    <i class="fa-solid fa-triangle-exclamation text-warning me-2"></i>
                                    Inventory Alerts
                                </h3>
                                <p class="small text-secondary mb-0">Items that need attention — low stock or out of stock.</p>
                            </div>
                            <a href="/panel/inventory" class="btn btn-sm btn-light border rounded-pill px-3 fw-semibold align-self-start align-self-md-center">
                                Manage Inventory
                                <i class="fa-solid fa-arrow-right ms-1 small"></i>
                            </a>
                        </div>

                        <div class="row g-3">
                            <div v-for="item in data.inventoryAlerts" :key="item.id" class="col-12 col-sm-6 col-lg-4 col-xl-3">
                                <div class="border rounded-4 p-3 h-100 d-flex align-items-start gap-3" :class="item.status === 'out_of_stock' ? 'border-danger bg-danger-subtle' : 'border-warning bg-warning-subtle'">
                                    <span class="stat-icon rounded-3 d-inline-flex align-items-center justify-content-center flex-shrink-0" :class="item.status === 'out_of_stock' ? 'bg-danger text-white' : 'bg-warning text-dark'" style="width: 36px; height: 36px; font-size: 0.85rem">
                                        <i :class="item.status === 'out_of_stock' ? 'fa-solid fa-xmark' : 'fa-solid fa-exclamation'"></i>
                                    </span>
                                    <div>
                                        <div class="fw-bold small">{{ item.name }}</div>
                                        <div class="small text-secondary">{{ item.category }}</div>
                                        <div class="small fw-bold mt-1" :class="item.status === 'out_of_stock' ? 'text-danger' : 'text-warning'">
                                            {{ item.quantity }} {{ item.unit }} left
                                            <span class="fw-normal text-secondary">(reorder: {{ item.reorder_level }})</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </overlay>

        <!-- Add Booking Modal -->
        <div class="modal fade" ref="addModal" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 rounded-4 shadow">
                    <div class="modal-header border-0 px-4 pt-4 pb-0">
                        <div>
                            <h4 class="modal-title fw-bold">New Appointment</h4>
                            <p class="small text-secondary mb-0">Fill in the details to create a new booking.</p>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body px-4 py-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">
                                    Customer Name
                                    <span class="text-danger">*</span>
                                </label>
                                <input v-model="form.customer_name" type="text" class="form-control rounded-4" placeholder="e.g. Juan Dela Cruz" />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Email</label>
                                <input v-model="form.customer_email" type="email" class="form-control rounded-4" placeholder="e.g. juan@email.com" />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Phone</label>
                                <input v-model="form.customer_phone" type="text" class="form-control rounded-4" placeholder="e.g. 09171234567" />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">
                                    Service
                                    <span class="text-danger">*</span>
                                </label>
                                <select v-model="form.service_id" class="form-select rounded-4">
                                    <option value="">Select a service</option>
                                    <option v-for="s in servicesList" :key="s.id" :value="s.id">
                                        {{ s.name }}
                                        <span v-if="s.price">&mdash; ₱{{ Number(s.price).toLocaleString("en-PH", { minimumFractionDigits: 2 }) }}</span>
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">
                                    Vehicle Size
                                    <span class="text-danger">*</span>
                                </label>
                                <select v-model="form.size_id" class="form-select rounded-4">
                                    <option value="">Select a size</option>
                                    <option v-for="s in sizesList" :key="s.id" :value="s.id">{{ s.name }} ({{ Number(s.multiplier).toFixed(2) }}×)</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold">
                                    Date
                                    <span class="text-danger">*</span>
                                </label>
                                <input v-model="form.date" type="date" class="form-control rounded-4" />
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold">
                                    Time
                                    <span class="text-danger">*</span>
                                </label>
                                <input v-model="form.time" type="time" class="form-control rounded-4" />
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold">Notes</label>
                                <textarea v-model="form.notes" class="form-control rounded-4" rows="3" placeholder="Any special instructions..."></textarea>
                            </div>
                            <div class="col-12" v-if="formComputedPrice">
                                <div class="p-3 rounded-4 bg-light d-flex justify-content-between align-items-center">
                                    <span class="fw-semibold small text-secondary">Estimated Price</span>
                                    <span class="fw-bold fs-5 text-info-emphasis">₱{{ formComputedPrice }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4 pt-0">
                        <button type="button" class="btn btn-light border rounded-4 px-4 py-2 fw-semibold" data-bs-dismiss="modal">Cancel</button>
                        <button @click="saveBooking" class="btn btn-info text-white rounded-4 px-4 py-2 fw-semibold btn-gradient" :disabled="saving">
                            <i class="fa-solid fa-plus me-2"></i>
                            {{ saving ? "Creating..." : "Create Appointment" }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Toast -->
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1090">
            <div ref="toast" class="toast rounded-4 border-0 shadow-lg" :class="toastClass" role="alert">
                <div class="toast-body d-flex align-items-center gap-2 px-4 py-3 fw-semibold">
                    <i :class="toastIcon"></i>
                    {{ toastMessage }}
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            loading: true,
            saving: false,
            servicesList: [],
            sizesList: [],
            form: { customer_name: "", customer_email: "", customer_phone: "", service_id: "", size_id: "", date: new Date().toISOString().split("T")[0], time: "08:00", notes: "" },
            toastMessage: "",
            toastClass: "text-bg-success",
            toastIcon: "fa-solid fa-circle-check",
            data: {
                todayRevenue: 0,
                revenueChange: 0,
                bookedSlots: 0,
                totalSlots: 18,
                completedToday: 0,
                inProgressCount: 0,
                topServices: [],
                recentAppointments: [],
                slots: [],
                today: "",
                weeklyRevenue: [],
                inventoryAlerts: [],
            },
        };
    },
    computed: {
        weeklyTotal() {
            return (this.data.weeklyRevenue || []).reduce((sum, d) => sum + d.total, 0);
        },
        weeklyMax() {
            return Math.max(...(this.data.weeklyRevenue || []).map((d) => d.total), 1);
        },
        formComputedPrice() {
            if (!this.form.service_id || !this.form.size_id) return null;
            const service = this.servicesList.find((s) => s.id == this.form.service_id);
            const size = this.sizesList.find((s) => s.id == this.form.size_id);
            if (!service?.price || !size?.multiplier) return null;
            return (Number(service.price) * Number(size.multiplier)).toLocaleString("en-PH", { minimumFractionDigits: 2 });
        },
    },
    mounted() {
        this._addModal = new bootstrap.Modal(this.$refs.addModal);

        // Listen for "Add Booking" button in page-actions (outside Vue template)
        document.addEventListener("click", (e) => {
            if (e.target.closest('[data-action="add-booking-dashboard"]')) {
                this.openAddBooking();
            }
        });

        this.fetchData();
    },
    methods: {
        async fetchData() {
            this.loading = true;
            try {
                const { data } = await axios.get("/panel/api/dashboard");
                this.data = data;
            } catch (e) {
                console.error("Failed to load dashboard data:", e);
            } finally {
                this.loading = false;
            }
        },
        formatNumber(val) {
            return Number(val || 0).toLocaleString("en-PH", { minimumFractionDigits: 0, maximumFractionDigits: 0 });
        },
        formatCompact(val) {
            if (val >= 1000) return (val / 1000).toFixed(1) + "k";
            return val.toLocaleString("en-PH", { minimumFractionDigits: 0 });
        },
        barHeight(total) {
            if (this.weeklyMax <= 0) return 5;
            return Math.max((total / this.weeklyMax) * 100, 4);
        },
        statusClass(status) {
            return (
                {
                    scheduled: "text-bg-primary",
                    in_progress: "text-bg-warning",
                    completed: "text-bg-success",
                    cancelled: "text-bg-danger",
                }[status] || "text-bg-secondary"
            );
        },
        statusLabel(status) {
            return (
                {
                    scheduled: "Scheduled",
                    in_progress: "In Progress",
                    completed: "Completed",
                    cancelled: "Cancelled",
                }[status] || status
            );
        },

        // ── Add Booking ──
        async openAddBooking() {
            this.resetForm();
            // Fetch services & sizes if not yet loaded
            if (this.servicesList.length === 0 || this.sizesList.length === 0) {
                try {
                    const { data } = await axios.get("/panel/api/appointments", { params: { page: 1 } });
                    this.servicesList = data.services || [];
                    this.sizesList = data.sizes || [];
                } catch (e) {
                    console.error("Failed to load form data:", e);
                }
            }
            this._addModal.show();
        },
        async saveBooking() {
            this.saving = true;
            try {
                await axios.post("/panel/api/appointments", this.form);
                this._addModal.hide();
                this.resetForm();
                this.showToast("Appointment created successfully.", "success");
                this.fetchData();
            } catch (e) {
                const msg = e.response?.data?.message || "Failed to create appointment.";
                this.showToast(msg, "danger");
            } finally {
                this.saving = false;
            }
        },
        resetForm() {
            this.form = { customer_name: "", customer_email: "", customer_phone: "", service_id: "", size_id: "", date: new Date().toISOString().split("T")[0], time: "08:00", notes: "" };
        },
        showToast(message, type = "success") {
            this.toastMessage = message;
            this.toastClass = type === "success" ? "text-bg-success" : "text-bg-danger";
            this.toastIcon = type === "success" ? "fa-solid fa-circle-check" : "fa-solid fa-circle-xmark";
            const toast = new bootstrap.Toast(this.$refs.toast, { delay: 3000 });
            toast.show();
        },
    },
};
</script>
