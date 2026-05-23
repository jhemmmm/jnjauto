<template>
    <div class="booking-page">
        <!-- ── Page Header ── -->
        <div class="booking-hero text-center py-5">
            <div class="container">
                <span class="d-inline-flex align-items-center gap-2 rounded-pill px-3 py-2 mb-3 small fw-semibold booking-badge">
                    <i class="fa-solid fa-calendar-check"></i>
                    Online Booking
                </span>
                <h1 class="display-5 fw-bold mb-2">Book an Appointment</h1>
                <p class="text-muted mb-0">Schedule your car wash in three easy steps</p>
            </div>
        </div>

        <div class="container pb-5">
            <!-- ── Step Progress ── -->
            <div class="row justify-content-center mb-5">
                <div class="col-12 col-md-8 col-lg-6">
                    <div class="stepper d-flex align-items-start">
                        <div class="stepper-step text-center flex-shrink-0" :class="{ active: step >= 1, done: step > 1 }" role="button" @click="goToStep(1)">
                            <div class="stepper-circle mx-auto">
                                <i v-if="step > 1" class="fa-solid fa-check"></i>
                                <span v-else>1</span>
                            </div>
                            <span class="stepper-label">Service</span>
                        </div>
                        <div class="stepper-line flex-fill" :class="{ active: step > 1 }"></div>
                        <div class="stepper-step text-center flex-shrink-0" :class="{ active: step >= 2, done: step > 2 }" :role="step >= 2 ? 'button' : ''" @click="goToStep(2)">
                            <div class="stepper-circle mx-auto">
                                <i v-if="step > 2" class="fa-solid fa-check"></i>
                                <span v-else>2</span>
                            </div>
                            <span class="stepper-label">Schedule</span>
                        </div>
                        <div class="stepper-line flex-fill" :class="{ active: step > 2 }"></div>
                        <div class="stepper-step text-center flex-shrink-0" :class="{ active: step >= 3 }">
                            <div class="stepper-circle mx-auto">3</div>
                            <span class="stepper-label">Confirm</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ═══════════════════════════════ -->
            <!-- STEP 1 – Service & Vehicle      -->
            <!-- ═══════════════════════════════ -->
            <div v-show="step === 1">
                <div class="row justify-content-center">
                    <div class="col-12 col-lg-10">
                        <!-- Services -->
                        <div class="card border-0 rounded-4 shadow-sm mb-4">
                            <div class="card-body p-4">
                                <h5 class="fw-bold mb-1">
                                    <i class="fa-solid fa-spray-can-sparkles text-primary me-2"></i>
                                    Choose a Service
                                </h5>
                                <p class="text-muted small mb-3">Select the wash package that best suits your needs.</p>
                                <div class="row g-3">
                                    <div v-for="(service, idx) in services" :key="service.id" class="col-12 col-sm-6 col-xl-3">
                                        <div class="svc-card h-100" :class="{ active: state.selectedService === service.id }" @click="state.selectedService = service.id" role="button">
                                            <div class="svc-card-check" v-if="state.selectedService === service.id">
                                                <i class="fa-solid fa-circle-check"></i>
                                            </div>
                                            <div class="svc-card-icon mb-3">
                                                <i :class="serviceIcon(idx)"></i>
                                            </div>
                                            <div class="fw-bold mb-1">{{ service.name }}</div>
                                            <div class="text-muted small mb-2" v-if="service.description">{{ service.description }}</div>
                                            <div class="mt-auto">
                                                <span class="svc-card-price">₱{{ formatPrice(service.price) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Vehicle Size -->
                        <div class="card border-0 rounded-4 shadow-sm mb-4">
                            <div class="card-body p-4">
                                <h5 class="fw-bold mb-1">
                                    <i class="fa-solid fa-car text-primary me-2"></i>
                                    Vehicle Size
                                </h5>
                                <p class="text-muted small mb-3">Pricing adjusts based on your vehicle size.</p>
                                <div class="row g-2 g-md-3">
                                    <div v-for="size in sizes" :key="size.id" class="col-12 col-md-4">
                                        <button type="button" class="size-btn w-100 h-100" :class="{ active: state.selectedSize === size.id }" :aria-pressed="state.selectedSize === size.id" @click="state.selectedSize = size.id">
                                            <span class="size-btn-main">
                                                <span>
                                                    <span class="size-btn-name">{{ size.name }}</span>
                                                    <span v-if="size.description" class="size-btn-description">{{ size.description }}</span>
                                                </span>
                                                <span v-if="size.multiplier && Number(size.multiplier) !== 1" class="size-btn-multiplier">×{{ formatMultiplier(size.multiplier) }}</span>
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 1 Footer -->
                        <div class="d-flex align-items-center justify-content-between rounded-4 bg-light p-3 px-4">
                            <div>
                                <div class="text-muted small">Estimated Price</div>
                                <div class="h4 fw-bold text-primary mb-0">₱{{ computedPrice }}</div>
                            </div>
                            <button class="btn btn-primary btn-lg rounded-pill px-4" @click="goToStep(2)">
                                Continue
                                <i class="fa-solid fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ═══════════════════════════════ -->
            <!-- STEP 2 – Date & Time            -->
            <!-- ═══════════════════════════════ -->
            <div v-show="step === 2">
                <div class="row justify-content-center">
                    <div class="col-12 col-lg-10">
                        <!-- Date -->
                        <div class="card border-0 rounded-4 shadow-sm mb-4">
                            <div class="card-body p-4">
                                <h5 class="fw-bold mb-1">
                                    <i class="fa-regular fa-calendar text-primary me-2"></i>
                                    Select Date
                                </h5>
                                <p class="text-muted small mb-3">Choose your preferred appointment date.</p>
                                <div class="row align-items-center">
                                    <div class="col-md-5 mb-2 mb-md-0">
                                        <input v-model="state.selectedDate" :min="new Date().toISOString().split('T')[0]" type="date" class="form-control form-control-lg" />
                                    </div>
                                    <div class="col-md-7">
                                        <span class="text-muted">
                                            <i class="fa-regular fa-calendar-check me-1"></i>
                                            {{ formattedDate }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Time Slots -->
                        <div class="card border-0 rounded-4 shadow-sm mb-4">
                            <div class="card-body p-4">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                                    <h5 class="fw-bold mb-0">
                                        <i class="fa-regular fa-clock text-primary me-2"></i>
                                        Available Times
                                    </h5>
                                    <div class="d-flex gap-3 small text-muted">
                                        <span class="d-inline-flex align-items-center gap-1">
                                            <span class="legend-dot bg-success"></span>
                                            Open
                                        </span>
                                        <span class="d-inline-flex align-items-center gap-1">
                                            <span class="legend-dot bg-warning"></span>
                                            Filling
                                        </span>
                                        <span class="d-inline-flex align-items-center gap-1">
                                            <span class="legend-dot bg-secondary"></span>
                                            Unavailable
                                        </span>
                                    </div>
                                </div>

                                <overlay :show="loading">
                                    <div v-if="morningSlots.length" class="mb-4">
                                        <div class="slot-group-label">
                                            <i class="fa-solid fa-sun"></i>
                                            Morning
                                        </div>
                                        <div class="row g-2">
                                            <div v-for="slot in morningSlots" :key="slot.time" class="col-4 col-sm-3 col-lg-2">
                                                <button type="button" class="time-btn w-100" :class="getSlotClass(slot)" :disabled="slot.available === 0 || slot.past" @click="selectSlot(slot)">
                                                    <div class="time-btn-label">{{ formatTime12(slot.time) }}</div>
                                                    <div class="time-btn-cap">{{ slot.available }}/{{ config.SLOT_CAPACITY }}</div>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-if="afternoonSlots.length">
                                        <div class="slot-group-label">
                                            <i class="fa-solid fa-cloud-sun"></i>
                                            Afternoon
                                        </div>
                                        <div class="row g-2">
                                            <div v-for="slot in afternoonSlots" :key="slot.time" class="col-4 col-sm-3 col-lg-2">
                                                <button type="button" class="time-btn w-100" :class="getSlotClass(slot)" :disabled="slot.available === 0 || slot.past" @click="selectSlot(slot)">
                                                    <div class="time-btn-label">{{ formatTime12(slot.time) }}</div>
                                                    <div class="time-btn-cap">{{ slot.available }}/{{ config.SLOT_CAPACITY }}</div>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </overlay>
                            </div>
                        </div>

                        <!-- Step 2 Footer -->
                        <div class="d-flex justify-content-between">
                            <button class="btn btn-light rounded-pill px-4" @click="goToStep(1)">
                                <i class="fa-solid fa-arrow-left me-2"></i>
                                Back
                            </button>
                            <button class="btn btn-primary btn-lg rounded-pill px-4" :disabled="!state.selectedSlot" @click="goToStep(3)">
                                Continue
                                <i class="fa-solid fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ═══════════════════════════════ -->
            <!-- STEP 3 – Details & Confirm      -->
            <!-- ═══════════════════════════════ -->
            <div v-show="step === 3">
                <div class="row justify-content-center">
                    <!-- Customer Form -->
                    <div class="col-12 col-lg-6 mb-4">
                        <div class="card border-0 rounded-4 shadow-sm">
                            <div class="card-body p-4">
                                <h5 class="fw-bold mb-1">
                                    <i class="fa-regular fa-user text-primary me-2"></i>
                                    Your Information
                                </h5>
                                <p class="text-muted small mb-4">We'll use this to confirm your booking.</p>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold small">Full Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa-regular fa-user text-muted"></i></span>
                                        <input v-model="form.name" class="form-control" placeholder="Juan Dela Cruz" />
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold small">Email Address</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa-regular fa-envelope text-muted"></i></span>
                                        <input v-model="form.email" type="email" class="form-control" placeholder="juan@example.com" />
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold small">Mobile Number</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa-solid fa-phone text-muted"></i></span>
                                        <input v-model="form.phone" class="form-control" placeholder="09xx xxx xxxx" />
                                    </div>
                                </div>
                                <div>
                                    <label class="form-label fw-semibold small">
                                        Notes
                                        <span class="fw-normal text-muted">(optional)</span>
                                    </label>
                                    <textarea v-model="form.notes" class="form-control" rows="3" placeholder="Any special requests or instructions..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Summary Sidebar -->
                    <div class="col-12 col-lg-4 mb-4">
                        <div class="card border-0 rounded-4 shadow-sm sticky-top" style="top: 100px">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <h5 class="fw-bold mb-0">Booking Summary</h5>
                                    <span class="badge rounded-pill text-bg-primary">
                                        <span class="badge-dot"></span>
                                        Live
                                    </span>
                                </div>

                                <div class="rounded-4 bg-light p-3 mb-3">
                                    <div class="summary-line">
                                        <span>Service</span>
                                        <span class="fw-bold text-end">{{ selectedServiceName }}</span>
                                    </div>
                                    <div class="summary-line mt-2">
                                        <span>Vehicle</span>
                                        <span class="fw-bold">{{ selectedSizeName }}</span>
                                    </div>
                                    <div class="summary-line mt-2">
                                        <span>Date</span>
                                        <span class="fw-bold">{{ shortDate }}</span>
                                    </div>
                                    <div class="summary-line mt-2">
                                        <span>Time</span>
                                        <span class="fw-bold">{{ state.selectedSlot ? formatTime12(state.selectedSlot) : "—" }}</span>
                                    </div>
                                    <hr class="my-2" />
                                    <div class="summary-line">
                                        <span class="fw-bold">Total</span>
                                        <span class="fw-bold text-primary fs-5">₱{{ computedPrice }}</span>
                                    </div>
                                </div>

                                <button @click="submit" class="btn btn-primary btn-lg w-100 rounded-pill" :disabled="!canConfirm || loading">
                                    <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                                    <i v-else class="fa-solid fa-check me-2"></i>
                                    Confirm Booking
                                </button>
                                <p class="text-center text-muted small mt-2 mb-0">
                                    <i class="fa-solid fa-shield-halved me-1"></i>
                                    No payment required to book
                                </p>
                            </div>
                            <div class="card-footer bg-transparent border-0 px-4 pb-4 pt-0">
                                <div class="d-flex gap-2">
                                    <a class="flex-fill p-2 rounded-3 bg-light text-decoration-none text-center small" href="https://m.me/jnjauto" target="_blank" rel="noopener noreferrer">
                                        <i class="fa-brands fa-facebook-messenger text-primary"></i>
                                        Messenger
                                    </a>
                                    <a class="flex-fill p-2 rounded-3 bg-light text-decoration-none text-center small" href="tel:09xx" rel="noopener noreferrer">
                                        <i class="fa-solid fa-phone text-primary"></i>
                                        Call Us
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3 Back -->
                    <div class="col-12 col-lg-10">
                        <button class="btn btn-light rounded-pill px-4" @click="goToStep(2)">
                            <i class="fa-solid fa-arrow-left me-2"></i>
                            Back
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── Result Modal ── -->
        <div class="modal fade" id="appointmentModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 rounded-4 shadow">
                    <div class="modal-body text-center p-5">
                        <template v-if="error">
                            <div class="mb-3">
                                <i class="fa-solid fa-circle-xmark text-danger" style="font-size: 3.5rem"></i>
                            </div>
                            <h4 class="fw-bold mb-2">Booking Failed</h4>
                            <p class="text-muted mb-4">{{ error }}</p>
                            <button class="btn btn-primary rounded-pill px-4" data-bs-dismiss="modal">Try Again</button>
                        </template>
                        <template v-else>
                            <div class="mb-3">
                                <div class="success-ring mx-auto">
                                    <i class="fa-solid fa-check"></i>
                                </div>
                            </div>
                            <h4 class="fw-bold mb-2">Booking Confirmed!</h4>
                            <p class="text-muted mb-4">Your appointment has been successfully scheduled.</p>
                            <div v-if="bookingConfirmation" class="rounded-4 bg-light p-3 text-start mb-4">
                                <div class="summary-line">
                                    <span>Service</span>
                                    <span class="fw-bold">{{ bookingConfirmation.service }}</span>
                                </div>
                                <div class="summary-line mt-2">
                                    <span>Date</span>
                                    <span class="fw-bold">{{ bookingConfirmation.date }}</span>
                                </div>
                                <div class="summary-line mt-2">
                                    <span>Time</span>
                                    <span class="fw-bold">{{ bookingConfirmation.time }}</span>
                                </div>
                                <hr class="my-2" />
                                <div class="summary-line">
                                    <span class="fw-bold">Total</span>
                                    <span class="fw-bold text-primary">₱{{ bookingConfirmation.price }}</span>
                                </div>
                            </div>
                            <button class="btn btn-primary rounded-pill px-5" data-bs-dismiss="modal">Done</button>
                        </template>
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
            step: 1,

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
            bookingConfirmation: null,
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
        goToStep(n) {
            if (n > this.step + 1) return;
            if (n === 3 && !this.state.selectedSlot) return;
            this.step = n;
            window.scrollTo({ top: 0, behavior: "smooth" });
        },

        formatTime12(time) {
            const [h, m] = time.split(":").map(Number);
            const ampm = h >= 12 ? "PM" : "AM";
            const hour12 = h % 12 || 12;
            return `${hour12}:${String(m).padStart(2, "0")} ${ampm}`;
        },

        selectSlot(slot) {
            if (slot.available > 0 && !slot.past) {
                this.state.selectedSlot = slot.time;
            }
        },

        getSlotClass(slot) {
            if (this.state.selectedSlot === slot.time) return "selected";
            if (slot.past || slot.available === 0) return "unavailable";
            if (slot.available < this.config.SLOT_CAPACITY) return "filling";
            return "open";
        },

        serviceIcon(index) {
            const icons = ["fa-solid fa-droplet", "fa-solid fa-spray-can-sparkles", "fa-solid fa-car-side", "fa-solid fa-gem", "fa-solid fa-star"];
            return icons[index % icons.length];
        },

        formatPrice(price) {
            return Number(price).toLocaleString("en-PH", { minimumFractionDigits: 2 });
        },

        formatMultiplier(multiplier) {
            return Number(multiplier).toLocaleString("en-PH", { minimumFractionDigits: 0, maximumFractionDigits: 2 });
        },

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
                    if (this.sizes.length && !this.sizes.find((s) => s.id === this.state.selectedSize)) {
                        this.state.selectedSize = this.sizes[0].id;
                    }
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
                    if (this.services.length && !this.services.find((s) => s.id === this.state.selectedService)) {
                        this.state.selectedService = this.services[0].id;
                    }
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

            this.bookingConfirmation = {
                service: this.selectedServiceName,
                size: this.selectedSizeName,
                date: this.formattedDate,
                time: this.formatTime12(this.state.selectedSlot),
                price: this.computedPrice,
            };

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
                    this.form = { name: "", email: "", phone: "", notes: "" };
                    this.step = 1;
                    this.modal.show();
                })
                .catch((error) => {
                    this.error = error.response?.data?.message || "An error occurred while booking. Please try again.";
                    this.bookingConfirmation = null;
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
        morningSlots() {
            return this.state.slots.filter((s) => parseInt(s.time.split(":")[0]) < 12);
        },
        afternoonSlots() {
            return this.state.slots.filter((s) => parseInt(s.time.split(":")[0]) >= 12);
        },
        formattedDate() {
            if (!this.state.selectedDate) return "—";
            const d = new Date(this.state.selectedDate + "T00:00:00");
            return d.toLocaleDateString("en-US", { weekday: "long", month: "long", day: "numeric", year: "numeric" });
        },
        shortDate() {
            if (!this.state.selectedDate) return "—";
            const d = new Date(this.state.selectedDate + "T00:00:00");
            return d.toLocaleDateString("en-US", { month: "short", day: "numeric", year: "numeric" });
        },
        selectedServiceName() {
            return this.services.find((s) => s.id === this.state.selectedService)?.name || "—";
        },
        selectedSizeName() {
            return this.sizes.find((s) => s.id === this.state.selectedSize)?.name || "—";
        },
    },
    watch: {
        "state.selectedDate": function (newDate) {
            this.getAppointments();
        },
    },
};
</script>
