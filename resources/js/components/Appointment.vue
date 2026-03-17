<template>
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-md-4 mb-2">
                <div class="card hover-card border-0 rounded-4 shadow-sm">
                    <div class="card-body p-4">
                        <div class="step-dot mb-3">
                            <i class="fa-regular fa-calendar-days"></i>
                        </div>
                        <div class="fw-bold">Pick a date</div>
                        <div class="text-muted small">Only future dates allowed.</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4 mb-2">
                <div class="card hover-card border-0 rounded-4 shadow-sm">
                    <div class="card-body p-4">
                        <div class="step-dot mb-3">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                        <div class="fw-bold">Choose an open slot</div>
                        <div class="text-muted small">Full & past slots auto-disable.</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4 mb-2">
                <div class="card hover-card border-0 rounded-4 shadow-sm">
                    <div class="card-body p-4">
                        <div class="step-dot mb-3">
                            <i class="fa-solid fa-receipt"></i>
                        </div>
                        <div class="fw-bold">Confirm & track sales</div>
                        <div class="text-muted small">Perfect for dashboard reporting.</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <!-- Booking -->
            <div class="col-12 col-md-8 mb-2">
                <div class="card border-0 rounded-4 shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="h4 fw-bold mb-1">Book an Appointment</h2>
                        <hr class="my-3" />
                        <div class="row g-3 align-items-end">
                            <div class="col-md-5">
                                <label class="form-label fw-semibold">Select Date</label>
                                <input v-model="state.selectedDate" :min="new Date().toISOString().split('T')[0]" id="dateInput" type="date" class="form-control" />
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Service</label>
                                <select v-model="state.selectedService" id="serviceInput" class="form-select">
                                    <option v-for="service in services" :key="service.id" :value="service.id">{{ service.name }} — ₱{{ Number(service.price).toLocaleString("en-PH", { minimumFractionDigits: 2 }) }}</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Vehicle Size</label>
                                <select v-model="state.selectedSize" id="sizeInput" class="form-select">
                                    <option v-for="size in sizes" :key="size.id" :value="size.id">{{ size.name }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-4">
                            <div class="fw-bold">Available Time Slots</div>
                        </div>

                        <overlay :show="loading">
                            <div class="row mt-2">
                                <div v-for="(slot, index) in state.slots" class="col-12 col-md-6 col-xl-4 mb-2" :key="index">
                                    <button type="button" class="slot" :class="{ active: state.selectedSlot === slot.time }" :disabled="slot.available == 0 || slot.past" @click="state.selectedSlot = slot.available != 0 ? slot.time : null">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="slot-time">
                                                <i class="fa-regular fa-clock me-2"></i>
                                                {{ slot.time }}
                                            </div>
                                            <span v-if="slot.past" class="badge rounded-pill text-bg-secondary">PAST</span>
                                            <span v-else-if="slot.available != 0" class="badge rounded-pill text-bg-success">OPEN</span>
                                            <span v-else class="badge rounded-pill text-bg-danger">FULL</span>
                                        </div>
                                        <div class="slot-meta mt-1">Available: {{ slot.available }}/{{ config.SLOT_CAPACITY }}</div>
                                    </button>
                                </div>
                            </div>
                        </overlay>
                    </div>
                </div>
            </div>
            <!-- Booking Summary -->
            <div class="col-12 col-md-4 mb-2">
                <div class="card border-0 rounded-4 shadow-sm sticky-top" style="top: 100px">
                    <overlay :show="loading">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="fw-bold">Booking Summary</div>
                                <span class="badge rounded-pill text-bg-primary text-white">
                                    <i class="fa-solid fa-wand-sparkles"></i>
                                    Live
                                </span>
                            </div>

                            <div class="p-3 rounded-4 bg-light mb-3">
                                <div class="summary-line">
                                    <span>Date</span>
                                    <span class="fw-bold" id="sumDate">
                                        {{ state.selectedDate }}
                                    </span>
                                </div>
                                <div class="summary-line mt-2">
                                    <span>Time</span>
                                    <span class="fw-bold" id="sumTime">
                                        {{ state.selectedSlot || "-" }}
                                    </span>
                                </div>
                                <div class="summary-line mt-2">
                                    <span>Service</span>
                                    <span class="fw-bold" id="sumService">
                                        {{ services.find((s) => s.id === state.selectedService)?.name || "-" }}
                                    </span>
                                </div>
                                <div class="summary-line mt-2">
                                    <span>Vehicle Type</span>
                                    <span class="fw-bold" id="sumType">
                                        {{ sizes.find((s) => s.id === state.selectedSize)?.name || "-" }}
                                    </span>
                                </div>
                                <hr class="my-2" />
                                <div class="summary-line">
                                    <span class="fw-bold">Total Price</span>
                                    <span class="fw-bold text-primary fs-5" id="sumPrice">₱{{ computedPrice }}</span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Full Name</label>
                                <input v-model="form.name" class="form-control" required placeholder="Juan Dela Cruz" />
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Email Address</label>
                                <input v-model="form.email" type="email" class="form-control" required placeholder="juan@example.com" />
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Mobile Number</label>
                                <input v-model="form.phone" class="form-control" required placeholder="09xx xxx xxxx" />
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Notes (Optional)</label>
                                <textarea v-model="form.notes" class="form-control" rows="3" placeholder="Optional notes..."></textarea>
                            </div>

                            <button @click="submit" id="submitBtn" class="btn btn-primary btn-round w-100" type="submit" :disabled="!canConfirm">
                                <i class="bi bi-check2-circle me-1"></i>
                                Confirm Booking
                            </button>

                            <div class="text-center small text-muted mt-2">Select a time slot to enable.</div>

                            <hr class="my-4" />

                            <div class="d-flex gap-2">
                                <a class="flex-fill p-3 rounded-4 bg-light text-decoration-none" href="https://m.me/jnjauto" target="_blank">
                                    <div class="small text-muted">Support</div>
                                    <div class="fw-semibold">
                                        <i class="fa-brands fa-facebook-messenger"></i>
                                        Facebook
                                    </div>
                                </a>
                                <a class="flex-fill p-3 rounded-4 bg-light text-decoration-none" href="tel:09xx" target="_blank">
                                    <div class="small text-muted">Hotline</div>
                                    <div class="fw-semibold">
                                        <i class="fa-solid fa-headset"></i>
                                        09xx
                                    </div>
                                </a>
                            </div>
                        </div>
                    </overlay>
                </div>
            </div>
        </div>

        <!-- Result Modal -->
        <div class="modal fade" id="appointmentModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <span v-if="error">Booking Failed</span>
                            <span v-else>Booking Confirmed</span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div v-if="error" class="modal-body text-center">
                        <i class="fa-solid fa-circle-xmark text-danger fs-1 mb-3"></i>
                        <p class="mb-0">{{ error }}</p>
                    </div>
                    <div v-else class="modal-body text-center">
                        <i class="fa-solid fa-circle-check text-success fs-1 mb-3"></i>
                        <p class="mb-0">Your appointment has been successfully booked.</p>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            config: {
                OPEN_HOUR: 7,
                OPEN_MINUTE: 0,
                CLOSE_HOUR: 16,
                CLOSE_MINUTE: 0,
                SLOT_MINUTES: 30,
                SLOT_CAPACITY: 2,
            },

            state: {
                selectedDate: new Date().toISOString().split("T")[0],
                selectedService: 1,
                selectedSize: 1,
                selectedSlot: null,
                slots: [],
            },

            form: {
                name: "",
                email: "",
                phone: "",
                notes: "",
            },

            appointments: [],
            sizes: [],
            services: [],
            modal: null,
            error: null,
            loading: true,
        };
    },
    mounted() {
        this.getConfig();
        this.getSizes();
        this.getServices();
        this.modal = new bootstrap.Modal(document.getElementById("appointmentModal"));
        setInterval(() => {
            this.updatePastSlots();
        }, 1000 * 60);
    },
    methods: {
        getConfig: function () {
            axios
                .get("/appointment/api/config")
                .then((response) => {
                    const c = response.data;
                    this.config.OPEN_HOUR = c.open_hour;
                    this.config.OPEN_MINUTE = c.open_minute;
                    this.config.CLOSE_HOUR = c.close_hour;
                    this.config.CLOSE_MINUTE = c.close_minute;
                    this.config.SLOT_MINUTES = c.slot_minutes;
                    this.config.SLOT_CAPACITY = c.slot_capacity;
                    this.getAppointments();
                })
                .catch((error) => {
                    console.error(error);
                    // Fallback: load with defaults
                    this.getAppointments();
                });
        },
        getAppointments: function () {
            axios
                .post("/appointment/api/get", {
                    date: this.state.selectedDate,
                })
                .then((response) => {
                    this.appointments = response.data;
                    this.state.slots = [];
                    this.state.selectedSlot = null;
                    this.getSlots();
                    this.loading = false;
                })
                .catch((error) => {
                    console.error(error);
                });
        },
        getSizes: function () {
            axios
                .post("/size/api/get")
                .then((response) => {
                    this.sizes = response.data;
                })
                .catch((error) => {
                    console.error(error);
                });
        },
        getServices: function () {
            axios
                .post("/service/api/get")
                .then((response) => {
                    this.services = response.data;
                })
                .catch((error) => {
                    console.error(error);
                });
        },
        getSlots: function () {
            let cursor = this.config.OPEN_HOUR * 60 + this.config.OPEN_MINUTE;
            const end = this.config.CLOSE_HOUR * 60 + this.config.CLOSE_MINUTE;
            while (cursor < end) {
                const h = Math.floor(cursor / 60);
                const m = cursor % 60;
                const time = `${String(h).padStart(2, "0")}:${String(m).padStart(2, "0")}`;
                this.state.slots.push({
                    time: time,
                    available: this.config.SLOT_CAPACITY - (this.appointments.filter((a) => a.time === time).length || 0),
                    past: false,
                });
                cursor += this.config.SLOT_MINUTES;
            }
            this.updatePastSlots();
        },
        updatePastSlots: function () {
            const now = new Date();
            this.state.slots.forEach((slot) => {
                slot.past = new Date(`${this.state.selectedDate}T${slot.time}:00`) < now;
            });
        },

        submit: function () {
            this.loading = true;
            this.error = null;
            axios
                .post("/appointment/api/put", {
                    date: this.state.selectedDate,
                    time: this.state.selectedSlot,
                    service: this.state.selectedService,
                    size: this.state.selectedSize,
                    name: this.form.name,
                    email: this.form.email,
                    phone: this.form.phone,
                    notes: this.form.notes,
                })
                .then((response) => {
                    this.getAppointments();
                    this.form.name = "";
                    this.form.email = "";
                    this.form.phone = "";
                    this.form.notes = "";
                    this.modal.show();
                })
                .catch((error) => {
                    this.error = error.response?.data?.message || "An error occurred while booking. Please try again.";
                    this.loading = false;
                    this.modal.show();
                });
        },
    },
    computed: {
        canConfirm() {
            return !!(this.state.selectedSlot && this.form.name && this.form.email && this.form.phone);
        },
        computedPrice() {
            const service = this.services.find((s) => s.id === this.state.selectedService);
            const size = this.sizes.find((s) => s.id === this.state.selectedSize);
            if (!service?.price) return "0.00";
            const multiplier = Number(size?.multiplier ?? 1);
            return (Number(service.price) * multiplier).toLocaleString("en-PH", { minimumFractionDigits: 2 });
        },
    },
    watch: {
        "state.selectedDate": function (newDate) {
            this.getAppointments();
        },
    },
};
</script>
