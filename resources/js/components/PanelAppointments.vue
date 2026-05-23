<template>
    <div>
        <!-- Stats row -->
        <section class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="panel-card rounded-4 p-4 h-100 dash-stat-card">
                    <div class="small text-secondary fw-semibold mb-2">Today's Bookings</div>
                    <div class="h2 fw-bold mb-1">{{ stats.today }}</div>
                    <div class="small text-info fw-bold">
                        <i class="fa-solid fa-calendar-day me-1"></i>
                        Today
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="panel-card rounded-4 p-4 h-100 dash-stat-card">
                    <div class="small text-secondary fw-semibold mb-2">Scheduled</div>
                    <div class="h2 fw-bold mb-1">{{ stats.scheduled }}</div>
                    <div class="small text-primary fw-bold">
                        <i class="fa-solid fa-clock me-1"></i>
                        Upcoming
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="panel-card rounded-4 p-4 h-100 dash-stat-card">
                    <div class="small text-secondary fw-semibold mb-2">Completed</div>
                    <div class="h2 fw-bold mb-1">{{ stats.completed }}</div>
                    <div class="small text-success fw-bold">
                        <i class="fa-solid fa-circle-check me-1"></i>
                        Done
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="panel-card rounded-4 p-4 h-100 dash-stat-card">
                    <div class="small text-secondary fw-semibold mb-2">Cancelled</div>
                    <div class="h2 fw-bold mb-1">{{ stats.cancelled }}</div>
                    <div class="small text-danger fw-bold">
                        <i class="fa-solid fa-circle-xmark me-1"></i>
                        Cancelled
                    </div>
                </div>
            </div>
        </section>

        <!-- Filters -->
        <section class="panel-card rounded-4 p-4 mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-4">
                    <label class="form-label small fw-semibold text-secondary">Search</label>
                    <div class="input-group">
                        <span class="input-group-text border-end-0 bg-white rounded-start-4">
                            <i class="fa-solid fa-magnifying-glass text-secondary"></i>
                        </span>
                        <input v-model="filters.search" type="text" class="form-control border-start-0 rounded-end-4" placeholder="Name, email, phone..." @keyup.enter="fetchData(1)" />
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small fw-semibold text-secondary">Status</label>
                    <select v-model="filters.status" class="form-select rounded-4">
                        <option value="all">All Status</option>
                        <option value="scheduled">Scheduled</option>
                        <option value="in_progress">In Progress</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label small fw-semibold text-secondary">Date</label>
                    <input v-model="filters.date" type="date" class="form-control rounded-4" />
                </div>
                <div class="col-12 col-md-3 d-flex gap-2">
                    <button @click="fetchData(1)" class="btn btn-info text-white rounded-4 px-4 py-2 fw-semibold btn-gradient flex-grow-1">
                        <i class="fa-solid fa-filter me-2"></i>
                        Filter
                    </button>
                    <button @click="resetFilters" class="btn btn-light border rounded-4 px-3 py-2 fw-semibold">
                        <i class="fa-solid fa-rotate-left"></i>
                    </button>
                </div>
            </div>
        </section>

        <!-- Appointments table -->
        <section ref="appointmentTable" class="panel-card rounded-4 p-4 mb-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
                <div>
                    <h3 class="h4 fw-bold mb-1">All Appointments</h3>
                    <p class="small text-secondary mb-0">Manage booking status, service type, and customer details.</p>
                </div>
                <span class="badge text-bg-info rounded-pill px-3 py-2 align-self-start align-self-md-center">{{ pagination.total }} total</span>
            </div>

            <overlay :show="loading">
                <div v-if="appointments.length > 0">
                    <!-- Desktop Table -->
                    <div class="table-responsive d-none d-md-block">
                        <table class="table align-middle mb-0 admin-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Customer</th>
                                    <th>Service</th>
                                    <th>Size</th>
                                    <th>Price</th>
                                    <th>Date &amp; Time</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="a in appointments" :key="a.id">
                                    <td>
                                        <span class="fw-bold text-info-emphasis">{{ a.id }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ a.customer_name }}</div>
                                        <div class="small text-secondary" v-if="a.customer_email">{{ a.customer_email }}</div>
                                        <div class="small text-secondary" v-if="a.customer_phone">{{ a.customer_phone }}</div>
                                    </td>
                                    <td>{{ a.service?.name ?? "—" }}</td>
                                    <td>{{ a.size?.name ?? "—" }}</td>
                                    <td>
                                        <span class="fw-bold text-info-emphasis">₱{{ computePrice(a) }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ formatDate(a.date) }}</div>
                                        <div class="small text-secondary">{{ formatTime(a.time) }}</div>
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill px-3 py-2" :class="statusClass(a.status)">
                                            <span class="badge-dot"></span>
                                            {{ statusLabel(a.status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-end gap-1">
                                            <!-- Edit -->
                                            <button class="btn btn-sm btn-light border rounded-3 px-2" @click="editAppointment(a)" title="Edit appointment" aria-label="Edit appointment">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>

                                            <!-- Status dropdown -->
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-light border rounded-3 px-2" type="button" data-bs-toggle="dropdown" title="Change status" aria-label="Change status">
                                                    <i class="fa-solid fa-arrow-right-arrow-left"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end rounded-4 shadow-sm border-0 p-2">
                                                    <li class="dropdown-header small fw-bold text-secondary px-3">Change Status</li>
                                                    <li v-for="s in availableStatuses(a.status)" :key="s.key">
                                                        <button class="dropdown-item rounded-3 py-2 px-3" @click="updateStatus(a, s.key)">
                                                            <i :class="s.icon" class="me-2"></i>
                                                            {{ s.label }}
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>

                                            <!-- View -->
                                            <button class="btn btn-sm btn-light border rounded-3 px-2" @click="viewAppointment(a)" title="View appointment" aria-label="View appointment">
                                                <i class="fa-solid fa-eye"></i>
                                            </button>

                                            <!-- Delete -->
                                            <button class="btn btn-sm btn-light border rounded-3 px-2 text-danger" @click="deleteAppointment(a)" title="Delete appointment" aria-label="Delete appointment">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Cards -->
                    <div class="d-block d-md-none d-flex flex-column gap-3">
                        <div v-for="a in appointments" :key="'m-' + a.id" class="border rounded-4 p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <div class="fw-bold">{{ a.customer_name }}</div>
                                    <div class="small text-secondary" v-if="a.customer_phone">{{ a.customer_phone }}</div>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light border rounded-3 px-2" type="button" data-bs-toggle="dropdown" title="Appointment actions" aria-label="Appointment actions">
                                        <i class="fa-solid fa-ellipsis-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end rounded-4 shadow-sm border-0 p-2">
                                        <li>
                                            <button class="dropdown-item rounded-3 py-2 px-3" @click="viewAppointment(a)">
                                                <i class="fa-solid fa-eye text-info me-2"></i>
                                                View Details
                                            </button>
                                        </li>
                                        <li>
                                            <button class="dropdown-item rounded-3 py-2 px-3" @click="editAppointment(a)">
                                                <i class="fa-solid fa-pen-to-square text-secondary me-2"></i>
                                                Edit
                                            </button>
                                        </li>
                                        <li><hr class="dropdown-divider" /></li>
                                        <li class="dropdown-header small fw-bold text-secondary px-3">Change Status</li>
                                        <li v-for="s in availableStatuses(a.status)" :key="s.key">
                                            <button class="dropdown-item rounded-3 py-2 px-3" @click="updateStatus(a, s.key)">
                                                <i :class="s.icon" class="me-2"></i>
                                                {{ s.label }}
                                            </button>
                                        </li>
                                        <li><hr class="dropdown-divider" /></li>
                                        <li>
                                            <button class="dropdown-item rounded-3 py-2 px-3 text-danger" @click="deleteAppointment(a)">
                                                <i class="fa-solid fa-trash-can me-2"></i>
                                                Delete
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="d-flex flex-wrap gap-2 align-items-center mb-2">
                                <span class="badge rounded-pill px-3 py-2" :class="statusClass(a.status)">
                                    <span class="badge-dot"></span>
                                    {{ statusLabel(a.status) }}
                                </span>
                                <span class="badge text-bg-light border rounded-pill px-2 py-1 small">
                                    {{ a.service?.name ?? "—" }}
                                </span>
                                <span class="badge text-bg-light border rounded-pill px-2 py-1 small">
                                    {{ a.size?.name ?? "—" }}
                                </span>
                                <span class="badge text-bg-light border rounded-pill px-2 py-1 small fw-bold text-info-emphasis">₱{{ computePrice(a) }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="small text-secondary">
                                    <i class="fa-regular fa-calendar me-1"></i>
                                    {{ formatDate(a.date) }} · {{ formatTime(a.time) }}
                                </div>
                                <span class="small fw-bold text-info-emphasis">#{{ a.id }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <nav v-if="pagination.lastPage > 1" class="d-flex justify-content-center mt-4">
                        <ul class="pagination mb-0">
                            <li class="page-item" :class="{ disabled: pagination.currentPage <= 1 }">
                                <button class="page-link rounded-start-3" @click="fetchData(pagination.currentPage - 1)"><i class="fa-solid fa-chevron-left small"></i></button>
                            </li>
                            <li v-for="p in paginationPages" :key="p" class="page-item" :class="{ active: p === pagination.currentPage }">
                                <button class="page-link" @click="fetchData(p)">{{ p }}</button>
                            </li>
                            <li class="page-item" :class="{ disabled: pagination.currentPage >= pagination.lastPage }">
                                <button class="page-link rounded-end-3" @click="fetchData(pagination.currentPage + 1)"><i class="fa-solid fa-chevron-right small"></i></button>
                            </li>
                        </ul>
                    </nav>
                </div>

                <div v-else class="text-center py-5">
                    <div class="mb-3"><i class="fa-solid fa-calendar-xmark fa-3x text-secondary opacity-50"></i></div>
                    <h5 class="fw-bold text-secondary">No appointments found</h5>
                    <p class="text-secondary small">Try adjusting your filters or add a new booking.</p>
                    <button class="btn btn-info text-white rounded-4 px-4 py-2 fw-semibold btn-gradient" @click="showAddModal = true">
                        <i class="fa-solid fa-plus me-2"></i>
                        Add Booking
                    </button>
                </div>
            </overlay>
        </section>

        <!-- Add/Edit Modal -->
        <div class="modal fade" ref="addModal" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 rounded-4 shadow">
                    <div class="modal-header border-0 px-4 pt-4 pb-0">
                        <div>
                            <h4 class="modal-title fw-bold">{{ editingId ? "Edit Appointment" : "New Appointment" }}</h4>
                            <p class="small text-secondary mb-0">{{ editingId ? "Update the booking details below." : "Fill in the details to create a new booking." }}</p>
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
                                        <span v-if="s.price">— ₱{{ Number(s.price).toLocaleString("en-PH", { minimumFractionDigits: 2 }) }}</span>
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
                        <button @click="saveAppointment" class="btn btn-info text-white rounded-4 px-4 py-2 fw-semibold btn-gradient" :disabled="saving">
                            <i :class="editingId ? 'fa-solid fa-check' : 'fa-solid fa-plus'" class="me-2"></i>
                            {{ saving ? (editingId ? "Saving..." : "Creating...") : editingId ? "Save Changes" : "Create Appointment" }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- View Modal -->
        <div class="modal fade" ref="viewModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 rounded-4 shadow" v-if="viewing">
                    <div class="modal-header border-0 px-4 pt-4 pb-0">
                        <div>
                            <h4 class="modal-title fw-bold">Appointment Details</h4>
                            <p class="small text-secondary mb-0">Booking information overview.</p>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body px-4 py-4">
                        <div class="d-flex flex-column gap-3">
                            <div class="d-flex justify-content-between border-bottom pb-3">
                                <span class="text-secondary fw-semibold small">ID</span>
                                <span class="fw-bold text-info-emphasis">{{ viewing.id }}</span>
                            </div>
                            <div class="d-flex justify-content-between border-bottom pb-3">
                                <span class="text-secondary fw-semibold small">Customer</span>
                                <span class="fw-bold">{{ viewing.customer_name }}</span>
                            </div>
                            <div class="d-flex justify-content-between border-bottom pb-3">
                                <span class="text-secondary fw-semibold small">Email</span>
                                <span>{{ viewing.customer_email || "—" }}</span>
                            </div>
                            <div class="d-flex justify-content-between border-bottom pb-3">
                                <span class="text-secondary fw-semibold small">Phone</span>
                                <span>{{ viewing.customer_phone || "—" }}</span>
                            </div>
                            <div class="d-flex justify-content-between border-bottom pb-3">
                                <span class="text-secondary fw-semibold small">Service</span>
                                <span class="fw-bold">{{ viewing.service?.name ?? "—" }}</span>
                            </div>
                            <div class="d-flex justify-content-between border-bottom pb-3">
                                <span class="text-secondary fw-semibold small">Vehicle Size</span>
                                <span>{{ viewing.size?.name ?? "—" }}</span>
                            </div>
                            <div class="d-flex justify-content-between border-bottom pb-3">
                                <span class="text-secondary fw-semibold small">Price</span>
                                <span class="fw-bold text-info-emphasis">₱{{ computePrice(viewing) }}</span>
                            </div>
                            <div class="d-flex justify-content-between border-bottom pb-3">
                                <span class="text-secondary fw-semibold small">Date &amp; Time</span>
                                <span class="fw-bold">{{ formatDate(viewing.date) }} at {{ formatTime(viewing.time) }}</span>
                            </div>
                            <div class="d-flex justify-content-between border-bottom pb-3">
                                <span class="text-secondary fw-semibold small">Status</span>
                                <span class="badge rounded-pill px-3 py-2" :class="statusClass(viewing.status)">
                                    <span class="badge-dot"></span>
                                    {{ statusLabel(viewing.status) }}
                                </span>
                            </div>
                            <div>
                                <span class="text-secondary fw-semibold small d-block mb-1">Notes</span>
                                <span class="small">{{ viewing.notes || "No notes provided." }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4 pt-0">
                        <button type="button" class="btn btn-light border rounded-4 px-4 py-2 fw-semibold" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div class="modal fade" ref="deleteModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content border-0 rounded-4 shadow-lg">
                    <div class="modal-body text-center p-4">
                        <div class="mb-3">
                            <i class="fa-solid fa-triangle-exclamation fa-3x text-danger"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Delete Appointment</h5>
                        <p class="text-secondary mb-0">
                            Are you sure you want to delete
                            <strong>#{{ deleteTarget?.id }}</strong>
                            — {{ deleteTarget?.customer_name }}? This cannot be undone.
                        </p>
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4 pt-0 justify-content-center gap-2">
                        <button type="button" class="btn btn-light border rounded-4 px-4 py-2 fw-semibold" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger rounded-4 px-4 py-2 fw-semibold" @click="confirmDeleteAppointment" :disabled="deleting">
                            <span v-if="deleting" class="spinner-border spinner-border-sm me-2"></span>
                            <i v-else class="fa-solid fa-trash-can me-2"></i>
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Toast -->
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1090">
            <div ref="toast" class="toast rounded-4 border-0 shadow-lg" role="alert">
                <div class="toast-body d-flex align-items-center gap-2 px-4 py-3 fw-semibold" :class="toastClass">
                    <i :class="toastIcon" class="me-1"></i>
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
            appointments: [],
            stats: { today: 0, scheduled: 0, completed: 0, cancelled: 0 },
            servicesList: [],
            sizesList: [],
            pagination: { currentPage: 1, lastPage: 1, total: 0 },
            filters: { search: "", status: "all", date: "" },
            form: { customer_name: "", customer_email: "", customer_phone: "", service_id: "", size_id: "", date: new Date().toISOString().split("T")[0], time: "08:00", notes: "" },
            editingId: null,
            deleteTarget: null,
            deleting: false,
            viewing: null,
            addModalInstance: null,
            viewModalInstance: null,
            deleteModalInstance: null,
            showAddModal: false,
            toastMessage: "",
            toastClass: "text-bg-success",
            toastIcon: "fa-solid fa-circle-check",
        };
    },
    computed: {
        paginationPages() {
            const pages = [];
            const c = this.pagination.currentPage;
            const l = this.pagination.lastPage;
            for (let i = Math.max(1, c - 2); i <= Math.min(l, c + 2); i++) pages.push(i);
            return pages;
        },
        formComputedPrice() {
            if (!this.form.service_id || !this.form.size_id) return null;
            const service = this.servicesList.find((s) => s.id == this.form.service_id);
            const size = this.sizesList.find((s) => s.id == this.form.size_id);
            if (!service?.price || !size?.multiplier) return null;
            return (Number(service.price) * Number(size.multiplier)).toLocaleString("en-PH", { minimumFractionDigits: 2 });
        },
    },
    watch: {
        showAddModal(val) {
            if (val) {
                this.addModalInstance.show();
                this.showAddModal = false;
            }
        },
    },
    mounted() {
        this.addModalInstance = new bootstrap.Modal(this.$refs.addModal);
        this.viewModalInstance = new bootstrap.Modal(this.$refs.viewModal);
        this.deleteModalInstance = new bootstrap.Modal(this.$refs.deleteModal);

        // Listen for "Add Booking" button in page-actions (outside Vue template)
        document.addEventListener("click", (e) => {
            if (e.target.closest('[data-action="add-appointment"]')) {
                this.resetForm();
                this.addModalInstance.show();
            }
        });

        this.fetchData(1);
    },
    methods: {
        async fetchData(page = 1) {
            this.loading = true;
            try {
                const params = { page, ...this.filters };
                const { data } = await axios.get("/panel/api/appointments", { params });
                this.appointments = data.appointments.data;
                this.pagination = { currentPage: data.appointments.current_page, lastPage: data.appointments.last_page, total: data.appointments.total };
                this.stats = data.stats;
                this.servicesList = data.services;
                this.sizesList = data.sizes;
            } catch (e) {
                console.error(e);
            } finally {
                // scroll to appointment table
                this.$refs.appointmentTable.scrollIntoView({ behavior: "smooth" });
                this.loading = false;
            }
        },
        async storeAppointment() {
            this.saving = true;
            try {
                await axios.post("/panel/api/appointments", this.form);
                this.addModalInstance.hide();
                this.resetForm();
                this.showToast("Appointment created successfully.", "success");
                this.fetchData(1);
            } catch (e) {
                const msg = e.response?.data?.message || "Failed to create appointment.";
                this.showToast(msg, "danger");
            } finally {
                this.saving = false;
            }
        },
        async saveAppointment() {
            if (this.editingId) {
                this.saving = true;
                try {
                    await axios.put(`/panel/api/appointments/${this.editingId}`, this.form);
                    this.addModalInstance.hide();
                    this.resetForm();
                    this.showToast("Appointment updated successfully.", "success");
                    this.fetchData(this.pagination.currentPage);
                } catch (e) {
                    const msg = e.response?.data?.message || "Failed to update appointment.";
                    this.showToast(msg, "danger");
                } finally {
                    this.saving = false;
                }
            } else {
                this.storeAppointment();
            }
        },
        editAppointment(a) {
            this.editingId = a.id;
            this.form = {
                customer_name: a.customer_name || "",
                customer_email: a.customer_email || "",
                customer_phone: a.customer_phone || "",
                service_id: a.service_id || "",
                size_id: a.size_id || "",
                date: String(a.date).substring(0, 10),
                time: a.time || "08:00",
                notes: a.notes || "",
            };
            this.addModalInstance.show();
        },
        async updateStatus(appointment, status) {
            try {
                await axios.patch(`/panel/api/appointments/${appointment.id}/status`, { status });
                this.showToast("Status updated successfully.", "success");
                this.fetchData(this.pagination.currentPage);
            } catch (e) {
                this.showToast("Failed to update status.", "danger");
            }
        },
        async deleteAppointment(appointment) {
            this.deleteTarget = appointment;
            this.deleteModalInstance.show();
        },
        async confirmDeleteAppointment() {
            if (!this.deleteTarget) return;
            this.deleting = true;
            try {
                await axios.delete(`/panel/api/appointments/${this.deleteTarget.id}`);
                this.showToast("Appointment deleted successfully.", "success");
                this.fetchData(this.pagination.currentPage);
            } catch (e) {
                this.showToast(e.response?.data?.message || "Failed to delete appointment.", "danger");
            } finally {
                this.deleteModalInstance.hide();
                this.deleting = false;
            }
        },
        viewAppointment(a) {
            this.viewing = a;
            this.viewModalInstance.show();
        },
        resetForm() {
            this.editingId = null;
            this.form = { customer_name: "", customer_email: "", customer_phone: "", service_id: "", size_id: "", date: new Date().toISOString().split("T")[0], time: "08:00", notes: "" };
        },
        resetFilters() {
            this.filters = { search: "", status: "all", date: "" };
            this.fetchData(1);
        },
        showToast(message, type = "success") {
            this.toastMessage = message;
            this.toastClass = type === "success" ? "text-bg-success" : "text-bg-danger";
            this.toastIcon = type === "success" ? "fa-solid fa-circle-check" : "fa-solid fa-circle-xmark";
            const toast = new bootstrap.Toast(this.$refs.toast, { delay: 3000 });
            toast.show();
        },
        formatDate(d) {
            if (!d) return "—";
            const str = String(d).substring(0, 10);
            const date = new Date(str + "T00:00:00");
            return date.toLocaleDateString("en-US", { month: "short", day: "2-digit", year: "numeric" });
        },
        formatTime(t) {
            if (!t) return "—";
            const [h, m] = t.split(":");
            const hr = parseInt(h);
            return `${hr > 12 ? hr - 12 : hr || 12}:${m} ${hr >= 12 ? "PM" : "AM"}`;
        },
        statusClass(s) {
            return { scheduled: "text-bg-primary", in_progress: "text-bg-warning", completed: "text-bg-success", cancelled: "text-bg-danger" }[s] || "text-bg-secondary";
        },
        statusLabel(s) {
            return { scheduled: "Scheduled", in_progress: "In Progress", completed: "Completed", cancelled: "Cancelled" }[s] || s;
        },
        availableStatuses(current) {
            const all = [
                { key: "scheduled", label: "Scheduled", icon: "fa-solid fa-clock text-primary" },
                { key: "in_progress", label: "In Progress", icon: "fa-solid fa-spinner text-warning" },
                { key: "completed", label: "Completed", icon: "fa-solid fa-circle-check text-success" },
                { key: "cancelled", label: "Cancelled", icon: "fa-solid fa-circle-xmark text-danger" },
            ];
            return all.filter((s) => s.key !== current);
        },
        computePrice(a) {
            if (a.amount) return Number(a.amount).toLocaleString("en-PH", { minimumFractionDigits: 2 });
            const basePrice = Number(a.service?.price ?? 0);
            const multiplier = Number(a.size?.multiplier ?? 1);
            return (basePrice * multiplier).toLocaleString("en-PH", { minimumFractionDigits: 2 });
        },
    },
};
</script>
