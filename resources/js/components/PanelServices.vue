<template>
    <div>
        <!-- Stats row -->
        <section class="row g-3 mb-4">
            <div class="col-6 col-md-4">
                <div class="panel-card rounded-4 p-4 h-100 dash-stat-card">
                    <div class="small text-secondary fw-semibold mb-2">Total Services</div>
                    <div class="h2 fw-bold mb-1">{{ services.length }}</div>
                    <div class="small text-info fw-bold">
                        <i class="fa-solid fa-car-side me-1"></i>
                        Active packages
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4">
                <div class="panel-card rounded-4 p-4 h-100 dash-stat-card">
                    <div class="small text-secondary fw-semibold mb-2">Vehicle Sizes</div>
                    <div class="h2 fw-bold mb-1">{{ sizes.length }}</div>
                    <div class="small text-primary fw-bold">
                        <i class="fa-solid fa-ruler-combined me-1"></i>
                        Size categories
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="panel-card rounded-4 p-4 h-100 dash-stat-card">
                    <div class="small text-secondary fw-semibold mb-2">Price Range</div>
                    <div class="h2 fw-bold mb-1">
                        <template v-if="services.length > 0">₱{{ minPrice }} – ₱{{ maxPrice }}</template>
                        <template v-else>-</template>
                    </div>
                    <div class="small text-success fw-bold">
                        <i class="fa-solid fa-peso-sign me-1"></i>
                        Min to max
                    </div>
                </div>
            </div>
        </section>

        <!-- Services Table -->
        <section class="panel-card rounded-4 p-4 mb-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
                <div>
                    <h3 class="h4 fw-bold mb-1">Car Wash Services</h3>
                    <p class="small text-secondary mb-0">All available service packages and their pricing.</p>
                </div>
                <span class="badge text-bg-info rounded-pill px-3 py-2 align-self-start align-self-md-center">{{ services.length }} services</span>
            </div>

            <overlay :show="loading">
                <div v-if="services.length > 0" class="table-responsive">
                    <table class="table align-middle mb-0 admin-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Service Name</th>
                                <th>Description</th>
                                <th>Price</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="s in services" :key="s.id">
                                <td>
                                    <span class="fw-bold text-info-emphasis">{{ s.id }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ s.name }}</div>
                                </td>
                                <td>
                                    <div class="small text-secondary">{{ s.description || "-" }}</div>
                                </td>
                                <td>
                                    <span class="fw-bold text-info-emphasis">
                                        {{ s.price ? "₱" + Number(s.price).toLocaleString("en-PH", { minimumFractionDigits: 2 }) : "-" }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-end gap-1">
                                        <button class="btn btn-sm btn-light border rounded-3 px-2" @click="editService(s)" title="Edit service" aria-label="Edit service">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <button class="btn btn-sm btn-light border rounded-3 px-2 text-danger" @click="deleteService(s)" title="Delete service" aria-label="Delete service">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-else class="text-center py-5">
                    <div class="mb-3"><i class="fa-solid fa-car-side fa-3x text-secondary opacity-50"></i></div>
                    <h5 class="fw-bold text-secondary">No services yet</h5>
                    <p class="text-secondary small">Add your first car wash service to get started.</p>
                    <button class="btn btn-info text-white rounded-4 px-4 py-2 fw-semibold btn-gradient" @click="openAddService">
                        <i class="fa-solid fa-plus me-2"></i>
                        Add Service
                    </button>
                </div>
            </overlay>
        </section>

        <!-- Sizes Table -->
        <section class="panel-card rounded-4 p-4 mb-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
                <div>
                    <h3 class="h4 fw-bold mb-1">Vehicle Sizes</h3>
                    <p class="small text-secondary mb-0">Categories used when customers book an appointment.</p>
                </div>
                <span class="badge text-bg-info rounded-pill px-3 py-2 align-self-start align-self-md-center">{{ sizes.length }} sizes</span>
            </div>

            <overlay :show="loading">
                <div v-if="sizes.length > 0" class="table-responsive">
                    <table class="table align-middle mb-0 admin-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Size Name</th>
                                <th>Description</th>
                                <th>Multiplier</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="sz in sizes" :key="sz.id">
                                <td>
                                    <span class="fw-bold text-info-emphasis">{{ sz.id }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ sz.name }}</div>
                                </td>
                                <td>
                                    <div class="small text-secondary">{{ sz.description || "-" }}</div>
                                </td>
                                <td>
                                    <span class="badge text-bg-light border rounded-pill px-3 py-2 fw-bold">{{ Number(sz.multiplier).toFixed(2) }}×</span>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-end gap-1">
                                        <button class="btn btn-sm btn-light border rounded-3 px-2" @click="editSize(sz)" title="Edit size" aria-label="Edit size">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <button class="btn btn-sm btn-light border rounded-3 px-2 text-danger" @click="deleteSize(sz)" title="Delete size" aria-label="Delete size">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-else class="text-center py-5">
                    <div class="mb-3"><i class="fa-solid fa-ruler-combined fa-3x text-secondary opacity-50"></i></div>
                    <h5 class="fw-bold text-secondary">No vehicle sizes yet</h5>
                    <p class="text-secondary small">Add vehicle size categories for your bookings.</p>
                    <button class="btn btn-info text-white rounded-4 px-4 py-2 fw-semibold btn-gradient" @click="openAddSize">
                        <i class="fa-solid fa-plus me-2"></i>
                        Add Size
                    </button>
                </div>
            </overlay>
        </section>

        <!-- Service Modal (Add/Edit) -->
        <div class="modal fade" ref="serviceModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 rounded-4 shadow">
                    <div class="modal-header border-0 px-4 pt-4 pb-0">
                        <div>
                            <h4 class="modal-title fw-bold">{{ serviceForm.id ? "Edit Service" : "New Service" }}</h4>
                            <p class="small text-secondary mb-0">{{ serviceForm.id ? "Update service details and pricing." : "Add a new car wash service package." }}</p>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body px-4 py-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label small fw-semibold">
                                    Service Name
                                    <span class="text-danger">*</span>
                                </label>
                                <input v-model="serviceForm.name" type="text" class="form-control rounded-4" placeholder="e.g. Basic Wash" />
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold">Description</label>
                                <textarea v-model="serviceForm.description" class="form-control rounded-4" rows="3" placeholder="Brief description of the service..."></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold">
                                    Price (₱)
                                    <span class="text-danger">*</span>
                                </label>
                                <input v-model="serviceForm.price" type="number" class="form-control rounded-4" placeholder="e.g. 350.00" step="0.01" min="0" />
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold mb-1">Inventory used per service</label>
                                <div class="form-text small mb-2">Estimated consumption per completed service. Will be multiplied by the appointment size.</div>
                                <div v-if="serviceForm.inventory_items.length > 0" class="d-flex flex-column gap-2 mb-2">
                                    <div v-for="(row, idx) in serviceForm.inventory_items" :key="idx" class="d-flex gap-2 align-items-center">
                                        <select v-model="row.inventory_item_id" class="form-select form-select-sm rounded-4 flex-grow-1">
                                            <option :value="null" disabled>Select item...</option>
                                            <option v-for="item in inventoryItems" :key="item.id" :value="item.id">{{ item.name }} ({{ item.unit }})</option>
                                        </select>
                                        <input v-model="row.quantity_per_service" type="number" class="form-control form-control-sm rounded-4" style="max-width: 110px" placeholder="Qty" step="0.001" min="0" />
                                        <button type="button" class="btn btn-sm btn-light border rounded-4" @click="removeInventoryRow(idx)" title="Remove">
                                            <i class="fa-solid fa-xmark text-danger"></i>
                                        </button>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-light border rounded-4 fw-semibold" @click="addInventoryRow">
                                    <i class="fa-solid fa-plus me-1"></i>
                                    Add item
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4 pt-0">
                        <button type="button" class="btn btn-light border rounded-4 px-4 py-2 fw-semibold" data-bs-dismiss="modal">Cancel</button>
                        <button @click="saveService" class="btn btn-info text-white rounded-4 px-4 py-2 fw-semibold btn-gradient" :disabled="saving">
                            <i class="me-2" :class="serviceForm.id ? 'fa-solid fa-floppy-disk' : 'fa-solid fa-plus'"></i>
                            {{ saving ? "Saving..." : serviceForm.id ? "Save Changes" : "Create Service" }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Size Modal (Add/Edit) -->
        <div class="modal fade" ref="sizeModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 rounded-4 shadow">
                    <div class="modal-header border-0 px-4 pt-4 pb-0">
                        <div>
                            <h4 class="modal-title fw-bold">{{ sizeForm.id ? "Edit Vehicle Size" : "New Vehicle Size" }}</h4>
                            <p class="small text-secondary mb-0">{{ sizeForm.id ? "Update size category details." : "Add a new vehicle size category." }}</p>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body px-4 py-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label small fw-semibold">
                                    Size Name
                                    <span class="text-danger">*</span>
                                </label>
                                <input v-model="sizeForm.name" type="text" class="form-control rounded-4" placeholder="e.g. Sedan, SUV, Van" />
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold">Description</label>
                                <textarea v-model="sizeForm.description" class="form-control rounded-4" rows="3" placeholder="e.g. Standard 4-door vehicles like Vios, City..."></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold">
                                    Price Multiplier
                                    <span class="text-danger">*</span>
                                </label>
                                <input v-model="sizeForm.multiplier" type="number" class="form-control rounded-4" placeholder="e.g. 1.50" step="0.01" min="0.01" />
                                <div class="form-text small">Base service price is multiplied by this value. E.g. 1.00 = no change, 1.50 = +50%, 2.00 = double.</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4 pt-0">
                        <button type="button" class="btn btn-light border rounded-4 px-4 py-2 fw-semibold" data-bs-dismiss="modal">Cancel</button>
                        <button @click="saveSize" class="btn btn-info text-white rounded-4 px-4 py-2 fw-semibold btn-gradient" :disabled="saving">
                            <i class="me-2" :class="sizeForm.id ? 'fa-solid fa-floppy-disk' : 'fa-solid fa-plus'"></i>
                            {{ saving ? "Saving..." : sizeForm.id ? "Save Changes" : "Create Size" }}
                        </button>
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
                        <h5 class="fw-bold mb-2">{{ deleteTarget?.type === "size" ? "Delete Vehicle Size" : "Delete Service" }}</h5>
                        <p class="text-secondary mb-0">
                            Are you sure you want to delete
                            <strong>{{ deleteTarget?.name }}</strong>
                            ? This cannot be undone.
                        </p>
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4 pt-0 justify-content-center gap-2">
                        <button type="button" class="btn btn-light border rounded-4 px-4 py-2 fw-semibold" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger rounded-4 px-4 py-2 fw-semibold" @click="confirmDelete" :disabled="deleting">
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
            services: [],
            sizes: [],
            inventoryItems: [],
            serviceForm: { id: null, name: "", description: "", price: "", inventory_items: [] },
            sizeForm: { id: null, name: "", description: "", multiplier: 1.0 },
            deleteTarget: null,
            deleting: false,
            serviceModalInstance: null,
            sizeModalInstance: null,
            deleteModalInstance: null,
            toastMessage: "",
            toastClass: "text-bg-success",
            toastIcon: "fa-solid fa-circle-check",
        };
    },
    computed: {
        minPrice() {
            const prices = this.services.filter((s) => s.price).map((s) => Number(s.price));
            return prices.length ? Math.min(...prices).toLocaleString("en-PH") : "0";
        },
        maxPrice() {
            const prices = this.services.filter((s) => s.price).map((s) => Number(s.price));
            return prices.length ? Math.max(...prices).toLocaleString("en-PH") : "0";
        },
    },
    mounted() {
        this.serviceModalInstance = new bootstrap.Modal(this.$refs.serviceModal);
        this.sizeModalInstance = new bootstrap.Modal(this.$refs.sizeModal);
        this.deleteModalInstance = new bootstrap.Modal(this.$refs.deleteModal);

        document.addEventListener("click", (e) => {
            if (e.target.closest('[data-action="add-service"]')) this.openAddService();
            if (e.target.closest('[data-action="add-size"]')) this.openAddSize();
        });

        this.fetchData();
    },
    methods: {
        async fetchData() {
            this.loading = true;
            try {
                const [servicesRes, inventoryRes] = await Promise.all([axios.get("/panel/api/services"), axios.get("/panel/api/inventory", { params: { per_page: 1000 } })]);
                this.services = servicesRes.data.services;
                this.sizes = servicesRes.data.sizes;
                const items = inventoryRes.data.items;
                this.inventoryItems = Array.isArray(items) ? items : items?.data || [];
            } catch (e) {
                console.error(e);
            } finally {
                this.loading = false;
            }
        },
        openAddService() {
            this.serviceForm = { id: null, name: "", description: "", price: "", inventory_items: [] };
            this.serviceModalInstance.show();
        },
        editService(s) {
            this.serviceForm = {
                id: s.id,
                name: s.name,
                description: s.description || "",
                price: s.price || "",
                inventory_items: (s.inventoryItems || s.inventory_items || []).map((it) => ({
                    inventory_item_id: it.id,
                    quantity_per_service: it.pivot?.quantity_per_service ?? 0,
                })),
            };
            this.serviceModalInstance.show();
        },
        addInventoryRow() {
            this.serviceForm.inventory_items.push({ inventory_item_id: null, quantity_per_service: 0 });
        },
        removeInventoryRow(idx) {
            this.serviceForm.inventory_items.splice(idx, 1);
        },
        async saveService() {
            this.saving = true;
            try {
                if (this.serviceForm.id) {
                    await axios.put(`/panel/api/services/${this.serviceForm.id}`, this.serviceForm);
                    this.showToast("Service updated successfully.", "success");
                } else {
                    await axios.post("/panel/api/services", this.serviceForm);
                    this.showToast("Service created successfully.", "success");
                }
                this.serviceModalInstance.hide();
                this.fetchData();
            } catch (e) {
                this.showToast(e.response?.data?.message || "Failed to save service.", "danger");
            } finally {
                this.saving = false;
            }
        },
        async deleteService(s) {
            this.deleteTarget = { id: s.id, name: s.name, type: "service" };
            this.deleteModalInstance.show();
        },
        openAddSize() {
            this.sizeForm = { id: null, name: "", description: "", multiplier: 1.0 };
            this.sizeModalInstance.show();
        },
        editSize(sz) {
            this.sizeForm = { id: sz.id, name: sz.name, description: sz.description || "", multiplier: sz.multiplier || 1.0 };
            this.sizeModalInstance.show();
        },
        async saveSize() {
            this.saving = true;
            try {
                if (this.sizeForm.id) {
                    await axios.put(`/panel/api/sizes/${this.sizeForm.id}`, this.sizeForm);
                    this.showToast("Vehicle size updated successfully.", "success");
                } else {
                    await axios.post("/panel/api/sizes", this.sizeForm);
                    this.showToast("Vehicle size created successfully.", "success");
                }
                this.sizeModalInstance.hide();
                this.fetchData();
            } catch (e) {
                this.showToast(e.response?.data?.message || "Failed to save size.", "danger");
            } finally {
                this.saving = false;
            }
        },
        async deleteSize(sz) {
            this.deleteTarget = { id: sz.id, name: sz.name, type: "size" };
            this.deleteModalInstance.show();
        },
        async confirmDelete() {
            if (!this.deleteTarget) return;
            this.deleting = true;
            try {
                if (this.deleteTarget.type === "service") {
                    await axios.delete(`/panel/api/services/${this.deleteTarget.id}`);
                    this.showToast("Service deleted successfully.", "success");
                } else {
                    await axios.delete(`/panel/api/sizes/${this.deleteTarget.id}`);
                    this.showToast("Vehicle size deleted successfully.", "success");
                }
                this.fetchData();
            } catch (e) {
                this.showToast(e.response?.data?.message || "Failed to delete.", "danger");
            } finally {
                this.deleteModalInstance.hide();
                this.deleting = false;
            }
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
