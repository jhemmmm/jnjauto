<template>
    <div>
        <!-- Filters -->
        <section class="panel-card rounded-4 p-4 mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-12" :class="period === 'custom' ? 'col-md-2' : 'col-md-3'">
                    <label class="form-label small fw-semibold text-secondary">Period</label>
                    <select v-model="period" class="form-select rounded-4" @change="onPeriodChange">
                        <option value="today">Today</option>
                        <option value="week">This Week</option>
                        <option value="month">This Month</option>
                        <option value="year">This Year</option>
                        <option value="custom">Custom Range</option>
                    </select>
                </div>
                <template v-if="period === 'custom'">
                    <div class="col-6 col-md-2">
                        <label class="form-label small fw-semibold text-secondary">Start Date</label>
                        <input type="date" v-model="startDate" class="form-control rounded-4" />
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label small fw-semibold text-secondary">End Date</label>
                        <input type="date" v-model="endDate" class="form-control rounded-4" />
                    </div>
                </template>
                <div class="col-12" :class="period === 'custom' ? 'col-md-3' : 'col-md-6'">
                    <label class="form-label small fw-semibold text-secondary">Service</label>
                    <select v-model="serviceFilter" class="form-select rounded-4">
                        <option value="all">All Services</option>
                        <option v-for="s in data.services" :key="s.id" :value="s.id">{{ s.name }}</option>
                    </select>
                </div>
                <div class="col-12 col-md-3 d-flex gap-2">
                    <button class="btn btn-lg btn-info text-white rounded-4 px-4 py-2 fw-semibold btn-gradient flex-grow-1" @click="fetchData">
                        <i class="fa-solid fa-filter me-2"></i>
                        Filter
                    </button>
                    <button class="btn btn-lg btn-light border rounded-4 px-3 py-2 fw-semibold" @click="resetFilters">
                        <i class="fa-solid fa-rotate-left"></i>
                    </button>
                </div>
            </div>
        </section>

        <!-- Stats row -->
        <section class="row g-3 mb-4">
            <div class="col-6 col-xl-3">
                <div class="panel-card rounded-4 p-4 h-100 dash-stat-card">
                    <div class="small text-secondary fw-semibold mb-2">Total Revenue</div>
                    <div class="h2 fw-bold mb-1">₱{{ formatCurrency(data.totalRevenue) }}</div>
                    <div class="small fw-bold" :class="data.revenueChange >= 0 ? 'text-success' : 'text-danger'">
                        <i class="fa-solid me-1" :class="data.revenueChange >= 0 ? 'fa-arrow-up' : 'fa-arrow-down'"></i>
                        {{ Math.abs(data.revenueChange) }}% vs prev period
                    </div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="panel-card rounded-4 p-4 h-100 dash-stat-card">
                    <div class="small text-secondary fw-semibold mb-2">Transactions</div>
                    <div class="h2 fw-bold mb-1">{{ data.totalTransactions }}</div>
                    <div class="small fw-bold" :class="data.transactionsChange >= 0 ? 'text-success' : 'text-danger'">
                        <i class="fa-solid me-1" :class="data.transactionsChange >= 0 ? 'fa-arrow-up' : 'fa-arrow-down'"></i>
                        {{ Math.abs(data.transactionsChange) }}% vs prev period
                    </div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="panel-card rounded-4 p-4 h-100 dash-stat-card">
                    <div class="small text-secondary fw-semibold mb-2">Average Ticket</div>
                    <div class="h2 fw-bold mb-1">₱{{ formatCurrency(data.averageTicket) }}</div>
                    <div class="small text-info fw-bold">
                        <i class="fa-solid fa-receipt me-1"></i>
                        Per transaction
                    </div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="panel-card rounded-4 p-4 h-100 dash-stat-card">
                    <div class="small text-secondary fw-semibold mb-2">Period</div>
                    <div class="h5 fw-bold mb-1">{{ data.startDateFormatted }} – {{ data.endDateFormatted }}</div>
                    <div class="small text-primary fw-bold">
                        <i class="fa-solid fa-calendar-days me-1"></i>
                        {{ capitalize(period) }} view
                    </div>
                </div>
            </div>
        </section>

        <!-- Revenue Chart -->
        <section class="panel-card rounded-4 p-4 mb-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
                <div>
                    <h3 class="h4 fw-bold mb-1">Revenue Trend</h3>
                    <p class="small text-secondary mb-0">Daily revenue within the selected period.</p>
                </div>
                <span class="badge text-bg-info rounded-pill px-3 py-2 align-self-start align-self-md-center">{{ data.chartData.length }} days</span>
            </div>
            <div v-if="hasChartRevenue" class="chart-container" style="position: relative; height: 280px">
                <canvas ref="chartCanvas"></canvas>
            </div>
            <div v-else class="text-center py-5">
                <div class="mb-3"><i class="fa-solid fa-chart-area fa-3x text-secondary opacity-50"></i></div>
                <h5 class="fw-bold text-secondary">No revenue data</h5>
                <p class="text-secondary small">Complete some appointments to see revenue trends here.</p>
            </div>
        </section>

        <div class="row g-4 mb-4">
            <!-- Revenue by Service -->
            <div class="col-12 col-xl-6">
                <section class="panel-card rounded-4 p-4 h-100">
                    <div class="d-flex justify-content-between align-items-center gap-3 mb-3">
                        <div>
                            <h3 class="h4 fw-bold mb-1">Revenue by Service</h3>
                            <p class="small text-secondary mb-0">Breakdown of revenue per service type.</p>
                        </div>
                    </div>
                    <div v-if="data.revenueByService.length > 0" class="d-flex flex-column gap-3">
                        <div v-for="item in data.revenueByService" :key="item.service_name">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div class="fw-semibold">{{ item.service_name }}</div>
                                <div class="d-flex align-items-center gap-3">
                                    <span class="badge text-bg-light border rounded-pill px-2 py-1 small">{{ item.total_count }}×</span>
                                    <span class="fw-bold text-info-emphasis">₱{{ formatCurrency(item.total_revenue) }}</span>
                                </div>
                            </div>
                            <div class="progress rounded-pill" style="height: 8px">
                                <div class="progress-bar bg-info rounded-pill" :style="{ width: item.percentage + '%' }"></div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-4">
                        <i class="fa-solid fa-chart-pie fa-2x text-secondary opacity-50 mb-2"></i>
                        <p class="text-secondary small mb-0">No service data for this period.</p>
                    </div>
                </section>
            </div>

            <!-- Top Customers -->
            <div class="col-12 col-xl-6">
                <section class="panel-card rounded-4 p-4 h-100" style="max-height: 500px; overflow-y: auto">
                    <div class="d-flex justify-content-between align-items-center gap-3 mb-3">
                        <div>
                            <h3 class="h4 fw-bold mb-1">Top Customers</h3>
                            <p class="small text-secondary mb-0">Most valuable customers by spend.</p>
                        </div>
                    </div>
                    <div v-if="data.topCustomers.length > 0" class="d-flex flex-column gap-2">
                        <div v-for="(cust, idx) in data.topCustomers" :key="idx" class="d-flex align-items-center gap-3 p-3 rounded-4" :class="{ 'bg-light': idx === 0 }">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-info bg-opacity-10 text-info-emphasis fw-bold" style="width: 40px; height: 40px; min-width: 40px">
                                {{ idx + 1 }}
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <div class="fw-semibold text-truncate">{{ cust.customer_name }}</div>
                                <div class="small text-secondary text-truncate">{{ cust.customer_email || cust.customer_phone || "—" }}</div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold text-info-emphasis">₱{{ formatCurrency(cust.total_spent) }}</div>
                                <div class="small text-secondary">{{ cust.visit_count }} visit{{ cust.visit_count > 1 ? "s" : "" }}</div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-4">
                        <i class="fa-solid fa-users fa-2x text-secondary opacity-50 mb-2"></i>
                        <p class="text-secondary small mb-0">No customer data for this period.</p>
                    </div>
                </section>
            </div>
        </div>

        <!-- Sales Transactions Table -->
        <section class="panel-card rounded-4 p-4 mb-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
                <div>
                    <h3 class="h4 fw-bold mb-1">Sales Transactions</h3>
                    <p class="small text-secondary mb-0">All completed appointments with revenue captured.</p>
                </div>
                <div class="d-flex align-items-center gap-2 align-self-start align-self-md-center">
                    <span class="badge text-bg-success rounded-pill px-3 py-2">{{ data.sales.length }} sales</span>
                </div>
            </div>

            <overlay :show="loading">
                <div v-if="data.sales.length > 0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0 admin-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Customer</th>
                                    <th>Service</th>
                                    <th>Size</th>
                                    <th>Date &amp; Time</th>
                                    <th>Completed</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="sale in data.sales" :key="sale.id">
                                    <td>
                                        <span class="fw-bold text-info-emphasis">{{ sale.id }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ sale.customer_name }}</div>
                                        <div class="small text-secondary">{{ sale.customer_phone || sale.customer_email || "—" }}</div>
                                    </td>
                                    <td>
                                        <span class="badge text-bg-info bg-opacity-10 text-info-emphasis rounded-pill px-3 py-2">
                                            {{ sale.service?.name ?? "—" }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="small">{{ sale.size?.name ?? "—" }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ formatDate(sale.date) }}</div>
                                        <div class="small text-secondary">{{ sale.time }}</div>
                                    </td>
                                    <td>
                                        <div class="small">{{ sale.completed_at ? formatDateTime(sale.completed_at) : "—" }}</div>
                                    </td>
                                    <td class="text-end">
                                        <span class="fw-bold text-success fs-6">₱{{ formatCurrency(sale.amount || 0) }}</span>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr class="border-top-2">
                                    <td colspan="6" class="text-end fw-bold">Total:</td>
                                    <td class="text-end">
                                        <span class="fw-bold text-success fs-5">₱{{ formatCurrency(data.totalRevenue) }}</span>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div v-else class="text-center py-5">
                    <div class="mb-3"><i class="fa-solid fa-cash-register fa-3x text-secondary opacity-50"></i></div>
                    <h5 class="fw-bold text-secondary">No sales yet</h5>
                    <p class="text-secondary small">Completed appointments will appear here as sales transactions.</p>
                    <a href="/panel/appointments" class="btn btn-info text-white rounded-4 px-4 py-2 fw-semibold btn-gradient">
                        <i class="fa-solid fa-calendar-check me-2"></i>
                        Go to Appointments
                    </a>
                </div>
            </overlay>
        </section>
    </div>
</template>

<script>
import { Chart, CategoryScale, LinearScale, PointElement, LineElement, Filler, Tooltip, Legend, LineController } from "chart.js";

Chart.register(CategoryScale, LinearScale, PointElement, LineElement, Filler, Tooltip, Legend, LineController);

export default {
    data() {
        return {
            loading: true,
            period: "month",
            startDate: "",
            endDate: "",
            serviceFilter: "all",
            chartInstance: null,
            data: {
                totalRevenue: 0,
                totalTransactions: 0,
                averageTicket: 0,
                revenueChange: 0,
                transactionsChange: 0,
                revenueByService: [],
                chartData: [],
                topCustomers: [],
                sales: [],
                services: [],
                startDateFormatted: "",
                endDateFormatted: "",
            },
        };
    },
    computed: {
        hasChartRevenue() {
            return this.data.chartData.some((d) => d.revenue > 0);
        },
    },
    mounted() {
        // Listen for Export CSV button in page-actions
        document.addEventListener("click", (e) => {
            if (e.target.closest('[data-action="export-csv"]')) {
                this.exportCSV();
            }
        });

        this.fetchData();
    },
    methods: {
        onPeriodChange() {
            if (this.period !== "custom") {
                this.startDate = "";
                this.endDate = "";
                this.fetchData();
            }
        },
        resetFilters() {
            this.period = "month";
            this.startDate = "";
            this.endDate = "";
            this.serviceFilter = "all";
            this.fetchData();
        },
        exportCSV() {
            if (!this.data.sales || this.data.sales.length === 0) return;

            const headers = ["#", "Customer", "Email", "Phone", "Service", "Size", "Date", "Time", "Completed", "Amount"];
            const rows = this.data.sales.map((s) => [s.id, '"' + (s.customer_name || "").replace(/"/g, '""') + '"', '"' + (s.customer_email || "").replace(/"/g, '""') + '"', '"' + (s.customer_phone || "").replace(/"/g, '""') + '"', '"' + (s.service?.name || "—").replace(/"/g, '""') + '"', '"' + (s.size?.name || "—").replace(/"/g, '""') + '"', s.date ? String(s.date).substring(0, 10) : "", s.time || "", s.completed_at || "", s.amount || 0]);

            const csv = [headers.join(","), ...rows.map((r) => r.join(","))].join("\n");
            const blob = new Blob([csv], { type: "text/csv;charset=utf-8;" });
            const url = URL.createObjectURL(blob);
            const link = document.createElement("a");
            link.href = url;
            link.download = "sales-report-" + (this.data.startDateFormatted || "").replace(/\s/g, "-") + "-to-" + (this.data.endDateFormatted || "").replace(/\s/g, "-") + ".csv";
            link.click();
            URL.revokeObjectURL(url);
        },
        async fetchData() {
            this.loading = true;
            try {
                const params = { period: this.period };
                if (this.period === "custom") {
                    if (this.startDate) params.start_date = this.startDate;
                    if (this.endDate) params.end_date = this.endDate;
                }
                if (this.serviceFilter !== "all") params.service_id = this.serviceFilter;

                const { data } = await axios.get("/panel/api/sales", { params });
                this.data = data;
                this.period = data.period;
                this.startDate = data.startDate;
                this.endDate = data.endDate;

                this.$nextTick(() => this.renderChart());
            } catch (e) {
                console.error(e);
            } finally {
                this.loading = false;
            }
        },
        renderChart() {
            if (!this.$refs.chartCanvas || !Chart || !this.hasChartRevenue) return;

            if (this.chartInstance) this.chartInstance.destroy();

            const ctx = this.$refs.chartCanvas.getContext("2d");
            const gradient = ctx.createLinearGradient(0, 0, 0, 280);
            gradient.addColorStop(0, "rgba(44, 165, 221, 0.25)");
            gradient.addColorStop(1, "rgba(44, 165, 221, 0.02)");

            const chartData = this.data.chartData;

            this.chartInstance = new Chart(ctx, {
                type: "line",
                data: {
                    labels: chartData.map((d) => d.date),
                    datasets: [
                        {
                            label: "Revenue (₱)",
                            data: chartData.map((d) => d.revenue),
                            borderColor: "#2ca5dd",
                            backgroundColor: gradient,
                            borderWidth: 2.5,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: "#2ca5dd",
                            pointBorderColor: "#fff",
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: "#1e293b",
                            cornerRadius: 12,
                            padding: 12,
                            callbacks: {
                                label: (context) => {
                                    const idx = context.dataIndex;
                                    const count = chartData[idx].count;
                                    return "₱" + context.parsed.y.toLocaleString(undefined, { minimumFractionDigits: 2 }) + "  (" + count + " sale" + (count !== 1 ? "s" : "") + ")";
                                },
                            },
                        },
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { color: "#94a3b8", maxRotation: 45 } },
                        y: { beginAtZero: true, grid: { color: "#f1f5f9" }, ticks: { color: "#94a3b8", callback: (v) => "₱" + v.toLocaleString() } },
                    },
                    interaction: { intersect: false, mode: "index" },
                },
            });
        },
        formatCurrency(val) {
            return Number(val || 0).toLocaleString("en-PH", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },
        formatDate(d) {
            if (!d) return "—";
            const str = String(d).substring(0, 10);
            return new Date(str + "T00:00:00").toLocaleDateString("en-US", { month: "short", day: "2-digit", year: "numeric" });
        },
        formatDateTime(dt) {
            if (!dt) return "—";
            return new Date(dt).toLocaleDateString("en-US", { month: "short", day: "2-digit", year: "numeric", hour: "numeric", minute: "2-digit", hour12: true });
        },
        capitalize(s) {
            return s ? s.charAt(0).toUpperCase() + s.slice(1) : "";
        },
    },
    beforeUnmount() {
        if (this.chartInstance) this.chartInstance.destroy();
    },
};
</script>
