<template>
    <div>
        <!-- Loading -->
        <div v-if="loading" class="text-center py-5">
            <div class="spinner-border text-info" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>

        <div v-else>
            <!-- Stats Row -->
            <div class="row g-3 mb-4">
                <div class="col-sm-4">
                    <div class="panel-card rounded-4 dash-stat-card h-100">
                        <div class="p-3 p-md-4">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="text-secondary small fw-semibold mb-1">Total Users</div>
                                    <div class="h3 fw-bold mb-0">{{ stats.total }}</div>
                                </div>
                                <div class="stat-icon rounded-4 d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-users text-info"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="panel-card rounded-4 dash-stat-card h-100">
                        <div class="p-3 p-md-4">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="text-secondary small fw-semibold mb-1">Admins</div>
                                    <div class="h3 fw-bold mb-0">{{ stats.admins }}</div>
                                </div>
                                <div class="stat-icon rounded-4 d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-user-shield text-success"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="panel-card rounded-4 dash-stat-card h-100">
                        <div class="p-3 p-md-4">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="text-secondary small fw-semibold mb-1">Staff</div>
                                    <div class="h3 fw-bold mb-0">{{ stats.staff }}</div>
                                </div>
                                <div class="stat-icon rounded-4 d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-user-tie text-warning"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="panel-card rounded-4 mb-4">
                <div class="p-3 p-md-4">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Search</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 rounded-start-4">
                                    <i class="fa-solid fa-magnifying-glass text-secondary"></i>
                                </span>
                                <input type="text" class="form-control border-start-0 rounded-end-4" placeholder="Search by name or email..." v-model="search" @input="debounceSearch" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Role</label>
                            <select class="form-select rounded-4" v-model="filterRole" @change="fetchUsers">
                                <option value="all">All Roles</option>
                                <option v-for="role in roles" :key="role.id" :value="role.id">{{ role.name }}</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex justify-content-end">
                            <button class="btn btn-light border rounded-4 px-3 py-2 fw-semibold w-100" @click="resetFilters">
                                <i class="fa-solid fa-rotate-left me-2"></i>
                                Reset
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Users Table -->
            <div class="panel-card rounded-4 overflow-hidden">
                <div class="table-responsive">
                    <table class="table align-middle mb-0 admin-table">
                        <thead>
                            <tr>
                                <th class="ps-4 py-3 text-secondary small fw-semibold text-uppercase">User</th>
                                <th class="py-3 text-secondary small fw-semibold text-uppercase">Email</th>
                                <th class="py-3 text-secondary small fw-semibold text-uppercase">Role</th>
                                <th class="py-3 text-secondary small fw-semibold text-uppercase">Joined</th>
                                <th class="pe-4 py-3 text-secondary small fw-semibold text-uppercase text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="users.length === 0">
                                <td colspan="5" class="text-center text-secondary py-5">
                                    <i class="fa-solid fa-users-slash fa-2x mb-3 d-block text-secondary opacity-50"></i>
                                    No users found.
                                </td>
                            </tr>
                            <tr v-for="user in users" :key="user.id">
                                <td class="ps-4 py-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="user-avatar rounded-4 d-flex align-items-center justify-content-center flex-shrink-0">
                                            {{ userInitials(user.name) }}
                                        </div>
                                        <div class="fw-semibold">{{ user.name }}</div>
                                    </div>
                                </td>
                                <td class="py-3">
                                    <span class="text-secondary">{{ user.email }}</span>
                                </td>
                                <td class="py-3">
                                    <span class="badge rounded-pill" :class="roleBadgeClass(user.role)">
                                        {{ user.role ? user.role.name : "N/A" }}
                                    </span>
                                </td>
                                <td class="py-3">
                                    <span class="text-secondary small">{{ formatDate(user.created_at) }}</span>
                                </td>
                                <td class="pe-4 py-3 text-end">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <button class="btn btn-sm btn-light border rounded-3 px-2" @click="editUser(user)" title="Edit">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <button class="btn btn-sm btn-light border rounded-3 px-2 text-danger" @click="confirmDelete(user)" :disabled="user.id === currentUserId" title="Delete">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="pagination.lastPage > 1" class="d-flex justify-content-center py-3 border-top">
                    <nav>
                        <ul class="pagination mb-0 gap-1">
                            <li class="page-item" :class="{ disabled: pagination.currentPage === 1 }">
                                <button class="page-link rounded-4 border-0" @click="goToPage(pagination.currentPage - 1)">
                                    <i class="fa-solid fa-chevron-left"></i>
                                </button>
                            </li>
                            <li v-for="page in paginationPages" :key="page" class="page-item" :class="{ active: page === pagination.currentPage }">
                                <button class="page-link rounded-4 border-0" @click="goToPage(page)">{{ page }}</button>
                            </li>
                            <li class="page-item" :class="{ disabled: pagination.currentPage === pagination.lastPage }">
                                <button class="page-link rounded-4 border-0" @click="goToPage(pagination.currentPage + 1)">
                                    <i class="fa-solid fa-chevron-right"></i>
                                </button>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Add/Edit Modal -->
        <div class="modal fade" id="userModal" tabindex="-1" ref="userModal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 rounded-4 shadow-lg">
                    <div class="modal-header border-0 pb-0 px-4 pt-4">
                        <h5 class="modal-title fw-bold">
                            <i :class="isEditing ? 'fa-solid fa-user-pen' : 'fa-solid fa-user-plus'" class="me-2 text-info"></i>
                            {{ isEditing ? "Edit User" : "Add User" }}
                        </h5>
                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body px-4 pt-3 pb-4">
                        <form @submit.prevent="saveUser">
                            <div class="d-flex flex-column gap-3">
                                <div>
                                    <label class="form-label fw-semibold">Full Name</label>
                                    <input type="text" class="form-control rounded-4" v-model="form.name" required />
                                </div>
                                <div>
                                    <label class="form-label fw-semibold">Email Address</label>
                                    <input type="email" class="form-control rounded-4" v-model="form.email" required />
                                </div>
                                <div>
                                    <label class="form-label fw-semibold">Role</label>
                                    <select class="form-select rounded-4" v-model="form.role_id" required>
                                        <option value="" disabled>Select a role</option>
                                        <option v-for="role in roles" :key="role.id" :value="role.id">{{ role.name }}</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="form-label fw-semibold">
                                        Password
                                        <span v-if="isEditing" class="text-secondary fw-normal">(leave blank to keep current)</span>
                                    </label>
                                    <div class="input-group">
                                        <input :type="showPassword ? 'text' : 'password'" class="form-control rounded-start-4" v-model="form.password" :required="!isEditing" minlength="8" pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9]).{8,}" :placeholder="isEditing ? 'Leave blank to keep current' : '8+ chars with Aa, number, symbol'" title="Use at least 8 characters with uppercase, lowercase, number, and special character." />
                                        <button type="button" class="btn btn-light border rounded-end-4" @click="showPassword = !showPassword">
                                            <i :class="showPassword ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'" class="text-secondary"></i>
                                        </button>
                                    </div>
                                    <div class="text-secondary small mt-1">Use at least 8 characters with uppercase, lowercase, number, and special character.</div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <button type="button" class="btn btn-light border rounded-4 px-4 py-2 fw-semibold" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-info text-white rounded-4 px-4 py-2 fw-semibold btn-gradient" :disabled="saving">
                                    <span v-if="saving" class="spinner-border spinner-border-sm me-2"></span>
                                    <i v-else :class="isEditing ? 'fa-solid fa-check' : 'fa-solid fa-plus'" class="me-2"></i>
                                    {{ isEditing ? "Save Changes" : "Create User" }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deleteUserModal" tabindex="-1" ref="deleteModal">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content border-0 rounded-4 shadow-lg">
                    <div class="modal-body text-center p-4">
                        <div class="mb-3">
                            <i class="fa-solid fa-triangle-exclamation fa-3x text-danger"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Delete User</h5>
                        <p class="text-secondary mb-0">
                            Are you sure you want to delete
                            <strong>{{ deleteTarget?.name }}</strong>
                            ? This action cannot be undone.
                        </p>
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4 pt-0 justify-content-center gap-2">
                        <button type="button" class="btn btn-light border rounded-4 px-4 py-2 fw-semibold" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger rounded-4 px-4 py-2 fw-semibold" @click="deleteUser" :disabled="deleting">
                            <span v-if="deleting" class="spinner-border spinner-border-sm me-2"></span>
                            <i v-else class="fa-solid fa-trash me-2"></i>
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
            users: [],
            roles: [],
            stats: { total: 0, admins: 0, staff: 0 },
            search: "",
            filterRole: "all",
            pagination: {
                currentPage: 1,
                lastPage: 1,
                total: 0,
            },

            // Form
            form: { name: "", email: "", password: "", role_id: "" },
            isEditing: false,
            editingId: null,
            showPassword: false,
            saving: false,

            // Delete
            deleteTarget: null,
            deleting: false,

            // Toast
            toastMessage: "",
            toastClass: "text-bg-success",
            toastIcon: "fa-solid fa-circle-check",

            // Current user
            currentUserId: null,

            // Search debounce
            searchTimeout: null,
        };
    },

    computed: {
        paginationPages() {
            const pages = [];
            const current = this.pagination.currentPage;
            const last = this.pagination.lastPage;
            let start = Math.max(1, current - 2);
            let end = Math.min(last, current + 2);
            if (current <= 3) end = Math.min(last, 5);
            if (current >= last - 2) start = Math.max(1, last - 4);
            for (let i = start; i <= end; i++) pages.push(i);
            return pages;
        },
    },

    mounted() {
        this.fetchUsers();

        // Listen for the page-action button
        document.addEventListener("click", (e) => {
            const btn = e.target.closest('[data-action="add-user"]');
            if (btn) this.openAddModal();
        });
    },

    methods: {
        async fetchUsers(page = 1) {
            this.loading = this.users.length === 0;
            try {
                const params = new URLSearchParams();
                params.append("page", page);
                if (this.search) params.append("search", this.search);
                if (this.filterRole !== "all") params.append("role", this.filterRole);

                const { data } = await axios.get("/panel/api/users?" + params.toString());

                this.users = data.users.data;
                this.roles = data.roles;
                this.stats = data.stats;
                this.currentUserId = data.users.data.length ? null : null; // will get from meta

                this.pagination = {
                    currentPage: data.users.current_page,
                    lastPage: data.users.last_page,
                    total: data.users.total,
                };

                // Retrieve current user id from the profile endpoint
                if (!this.currentUserId) {
                    try {
                        const settingsRes = await axios.get("/panel/api/settings");
                        this.currentUserId = settingsRes.data.profile?.id || null;
                    } catch (e) {
                        /* ignore */
                    }
                }
            } catch (e) {
                console.error("Failed to load users", e);
            } finally {
                this.loading = false;
            }
        },

        debounceSearch() {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => this.fetchUsers(), 400);
        },

        resetFilters() {
            this.search = "";
            this.filterRole = "all";
            this.fetchUsers();
        },

        goToPage(page) {
            if (page < 1 || page > this.pagination.lastPage) return;
            this.fetchUsers(page);
        },

        openAddModal() {
            this.isEditing = false;
            this.editingId = null;
            this.form = { name: "", email: "", password: "", role_id: "" };
            this.showPassword = false;
            this.getModal("userModal").show();
        },

        editUser(user) {
            this.isEditing = true;
            this.editingId = user.id;
            this.form = {
                name: user.name,
                email: user.email,
                password: "",
                role_id: user.role_id || (user.role ? user.role.id : ""),
            };
            this.showPassword = false;
            this.getModal("userModal").show();
        },

        async saveUser() {
            this.saving = true;
            try {
                if (this.isEditing) {
                    const payload = { name: this.form.name, email: this.form.email, role_id: this.form.role_id };
                    if (this.form.password) payload.password = this.form.password;
                    const { data } = await axios.put("/panel/api/users/" + this.editingId, payload);
                    this.showToast(data.message, "success");
                } else {
                    const { data } = await axios.post("/panel/api/users", this.form);
                    this.showToast(data.message, "success");
                }
                this.getModal("userModal").hide();
                this.fetchUsers(this.pagination.currentPage);
            } catch (e) {
                const msg = e.response?.data?.message || e.response?.data?.errors?.[Object.keys(e.response?.data?.errors || {})[0]]?.[0] || "Failed to save user.";
                this.showToast(msg, "danger");
            } finally {
                this.saving = false;
            }
        },

        confirmDelete(user) {
            this.deleteTarget = user;
            this.getModal("deleteModal").show();
        },

        async deleteUser() {
            if (!this.deleteTarget) return;
            this.deleting = true;
            try {
                const { data } = await axios.delete("/panel/api/users/" + this.deleteTarget.id);
                this.showToast(data.message, "success");
                this.fetchUsers(this.pagination.currentPage);
            } catch (e) {
                this.showToast(e.response?.data?.message || "Failed to delete user.", "danger");
            } finally {
                this.getModal("deleteModal").hide();
                this.deleting = false;
            }
        },

        /* ── Helpers ─────────────────────── */

        userInitials(name) {
            return name
                .split(" ")
                .map((w) => w[0])
                .join("")
                .substring(0, 2)
                .toUpperCase();
        },

        roleBadgeClass(role) {
            if (!role) return "text-bg-secondary";
            switch (role.id) {
                case 2:
                    return "text-bg-success";
                case 3:
                    return "text-bg-info";
                default:
                    return "text-bg-secondary";
            }
        },

        formatDate(dateStr) {
            if (!dateStr) return "";
            const d = new Date(dateStr);
            return d.toLocaleDateString("en-US", { year: "numeric", month: "short", day: "numeric" });
        },

        getModal(refName) {
            if (!this._modalCache) this._modalCache = {};
            if (!this._modalCache[refName]) {
                this._modalCache[refName] = new bootstrap.Modal(this.$refs[refName]);
            }
            return this._modalCache[refName];
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

<style scoped>
.user-avatar {
    width: 38px;
    height: 38px;
    background: linear-gradient(135deg, var(--jnj-primary), #1a8ec0);
    color: #fff;
    font-size: 0.8rem;
    font-weight: 700;
}

.stat-icon {
    width: 48px;
    height: 48px;
    background: var(--jnj-bg);
    font-size: 1.25rem;
}
</style>
