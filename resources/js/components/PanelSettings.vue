<template>
    <div>
        <!-- Loading -->
        <div v-if="loading" class="text-center py-5">
            <div class="spinner-border text-info" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>

        <div v-else>
            <!-- Tab navigation -->
            <div class="panel-card rounded-4 mb-4">
                <div class="p-2 p-md-3">
                    <div class="d-flex flex-wrap gap-2">
                        <button v-for="tab in tabs" :key="tab.value" class="btn rounded-4 px-3 py-2 fw-semibold" :class="activeTab === tab.value ? 'btn-info text-white btn-gradient' : 'btn-light border'" @click="activeTab = tab.value">
                            <i :class="tab.icon" class="me-2"></i>
                            {{ tab.label }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- ═══════════════ Profile ═══════════════ -->
            <div v-if="activeTab === 'profile'">
                <div class="panel-card rounded-4">
                    <div class="p-3 p-md-4">
                        <h5 class="fw-bold mb-1">Profile Information</h5>
                        <p class="text-secondary small mb-4">Update your account name and email address.</p>

                        <form @submit.prevent="saveProfile">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Full Name</label>
                                    <input type="text" class="form-control rounded-4" v-model="profile.name" required />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Email Address</label>
                                    <input type="email" class="form-control rounded-4" v-model="profile.email" required />
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-info text-white rounded-4 px-4 py-2 fw-semibold btn-gradient" :disabled="saving.profile">
                                    <span v-if="saving.profile" class="spinner-border spinner-border-sm me-2"></span>
                                    <i v-else class="fa-solid fa-check me-2"></i>
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- ═══════════════ Password ═══════════════ -->
            <div v-if="activeTab === 'password'">
                <div class="panel-card rounded-4">
                    <div class="p-3 p-md-4">
                        <h5 class="fw-bold mb-1">Change Password</h5>
                        <p class="text-secondary small mb-4">Ensure your account uses a strong password for security.</p>

                        <form @submit.prevent="savePassword">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Current Password</label>
                                    <div class="input-group">
                                        <input :type="showPasswords.current ? 'text' : 'password'" class="form-control rounded-start-4" v-model="passwordForm.current_password" required />
                                        <button type="button" class="btn btn-light border rounded-end-4" @click="showPasswords.current = !showPasswords.current">
                                            <i :class="showPasswords.current ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'" class="text-secondary"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">New Password</label>
                                    <div class="input-group">
                                        <input :type="showPasswords.new ? 'text' : 'password'" class="form-control rounded-start-4" v-model="passwordForm.password" required minlength="8" />
                                        <button type="button" class="btn btn-light border rounded-end-4" @click="showPasswords.new = !showPasswords.new">
                                            <i :class="showPasswords.new ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'" class="text-secondary"></i>
                                        </button>
                                    </div>
                                    <div class="small mt-2" :class="passwordStrengthColor">
                                        <i class="fa-solid fa-shield-halved me-1"></i>
                                        {{ passwordStrengthLabel }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Confirm New Password</label>
                                    <div class="input-group">
                                        <input :type="showPasswords.confirm ? 'text' : 'password'" class="form-control rounded-start-4" v-model="passwordForm.password_confirmation" required />
                                        <button type="button" class="btn btn-light border rounded-end-4" @click="showPasswords.confirm = !showPasswords.confirm">
                                            <i :class="showPasswords.confirm ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'" class="text-secondary"></i>
                                        </button>
                                    </div>
                                    <div v-if="passwordForm.password_confirmation && passwordForm.password !== passwordForm.password_confirmation" class="small text-danger mt-2">
                                        <i class="fa-solid fa-circle-xmark me-1"></i>
                                        Passwords do not match
                                    </div>
                                    <div v-else-if="passwordForm.password_confirmation && passwordForm.password === passwordForm.password_confirmation" class="small text-success mt-2">
                                        <i class="fa-solid fa-circle-check me-1"></i>
                                        Passwords match
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-info text-white rounded-4 px-4 py-2 fw-semibold btn-gradient" :disabled="saving.password || !passwordValid">
                                    <span v-if="saving.password" class="spinner-border spinner-border-sm me-2"></span>
                                    <i v-else class="fa-solid fa-lock me-2"></i>
                                    Update Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- ═══════════════ Business ═══════════════ -->
            <div v-if="activeTab === 'business'">
                <div class="d-flex flex-column gap-4">
                    <!-- Business Info -->
                    <div class="panel-card rounded-4">
                        <div class="p-3 p-md-4">
                            <h5 class="fw-bold mb-1">Business Information</h5>
                            <p class="text-secondary small mb-4">Your car wash business details shown to customers.</p>

                            <form @submit.prevent="saveBusinessSettings">
                                <!-- Application Branding -->
                                <h6 class="fw-bold mb-3">
                                    <i class="fa-solid fa-palette me-2 text-info"></i>
                                    Application Branding
                                </h6>

                                <!-- Logo Upload -->
                                <div class="d-flex align-items-center gap-4 mb-4">
                                    <div class="position-relative">
                                        <div v-if="logoPreview || business.business_logo_url" class="rounded-4 overflow-hidden border" style="width: 80px; height: 80px">
                                            <img :src="logoPreview || business.business_logo_url" alt="Logo" class="w-100 h-100" style="object-fit: cover" />
                                        </div>
                                        <div v-else class="rounded-4 border d-flex align-items-center justify-content-center bg-light" style="width: 80px; height: 80px">
                                            <i class="fa-solid fa-droplet fa-2x text-info"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="btn btn-light border rounded-4 px-3 py-2 fw-semibold me-2" style="cursor: pointer">
                                            <i class="fa-solid fa-upload me-2"></i>
                                            Upload Logo
                                            <input type="file" class="d-none" accept="image/jpeg,image/png,image/webp,image/svg+xml" @change="onLogoSelected" ref="logoInput" />
                                        </label>
                                        <button v-if="logoPreview || business.business_logo_url" type="button" class="btn btn-outline-danger rounded-4 px-3 py-2 fw-semibold" @click="removeLogo">
                                            <i class="fa-solid fa-trash me-2"></i>
                                            Remove
                                        </button>
                                        <div class="form-text small mt-1">JPG, PNG, WebP, or SVG. Max 2MB.</div>
                                    </div>
                                </div>

                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Application Name (First Part)</label>
                                        <input type="text" class="form-control rounded-4" v-model="business.app_name_first" placeholder="Wash" />
                                        <div class="form-text small">
                                            Displayed in
                                            <span class="text-primary fw-bold">blue</span>
                                            .
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Application Name (Second Part)</label>
                                        <input type="text" class="form-control rounded-4" v-model="business.app_name_last" placeholder="Wise" />
                                        <div class="form-text small">
                                            Displayed in
                                            <span class="text-dark fw-bold">black</span>
                                            .
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="d-flex align-items-center gap-2 p-3 rounded-4 bg-light border">
                                            <span class="small text-secondary me-2">Preview:</span>
                                            <div v-if="logoPreview || business.business_logo_url" class="rounded-3 overflow-hidden border" style="width: 32px; height: 32px">
                                                <img :src="logoPreview || business.business_logo_url" alt="Logo" class="w-100 h-100" style="object-fit: cover" />
                                            </div>
                                            <div v-else class="brand-icon-box bg-primary text-white d-inline-flex align-items-center justify-content-center rounded-3" style="width: 32px; height: 32px; font-size: 14px">
                                                <i class="fa-solid fa-droplet"></i>
                                            </div>
                                            <span class="fw-bold fs-5">
                                                <span class="text-primary">{{ business.app_name_first || "JNJ" }}</span>
                                                <span class="text-dark">{{ business.app_name_last || "Auto" }}</span>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Business Details -->
                                <h6 class="fw-bold mb-3">
                                    <i class="fa-solid fa-building me-2 text-info"></i>
                                    Business Details
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Business Name</label>
                                        <input type="text" class="form-control rounded-4" v-model="business.business_name" required />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Contact Email</label>
                                        <input type="email" class="form-control rounded-4" v-model="business.business_email" required />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Phone Number</label>
                                        <input type="text" class="form-control rounded-4" v-model="business.business_phone" required />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Currency</label>
                                        <select class="form-select rounded-4" v-model="business.currency" required>
                                            <option value="PHP">PHP — Philippine Peso</option>
                                            <option value="USD">USD — US Dollar</option>
                                            <option value="EUR">EUR — Euro</option>
                                            <option value="GBP">GBP — British Pound</option>
                                            <option value="JPY">JPY — Japanese Yen</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Business Address</label>
                                        <input type="text" class="form-control rounded-4" v-model="business.business_address" required />
                                    </div>
                                </div>

                                <!-- Operating Hours -->
                                <h6 class="fw-bold mt-4 mb-3">
                                    <i class="fa-regular fa-clock me-2 text-info"></i>
                                    Operating Hours
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-3 col-6">
                                        <label class="form-label fw-semibold">Opening Time</label>
                                        <input type="time" class="form-control rounded-4" v-model="business.opening_time" required />
                                    </div>
                                    <div class="col-md-3 col-6">
                                        <label class="form-label fw-semibold">Closing Time</label>
                                        <input type="time" class="form-control rounded-4" v-model="business.closing_time" required />
                                    </div>
                                    <div class="col-md-3 col-6">
                                        <label class="form-label fw-semibold">Slot Duration (min)</label>
                                        <input type="number" class="form-control rounded-4" v-model="business.slot_duration" min="10" max="120" required />
                                    </div>
                                    <div class="col-md-3 col-6">
                                        <label class="form-label fw-semibold">Slot Capacity</label>
                                        <input type="number" class="form-control rounded-4" v-model="business.slot_capacity" min="1" max="20" required />
                                        <div class="form-text small">Cars per time slot</div>
                                    </div>
                                </div>

                                <!-- Timezone -->
                                <h6 class="fw-bold mt-4 mb-3">
                                    <i class="fa-solid fa-globe me-2 text-info"></i>
                                    Locale
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Timezone</label>
                                        <select class="form-select rounded-4" v-model="business.timezone" required>
                                            <option value="Asia/Manila">Asia/Manila (GMT+8)</option>
                                            <option value="Asia/Tokyo">Asia/Tokyo (GMT+9)</option>
                                            <option value="Asia/Singapore">Asia/Singapore (GMT+8)</option>
                                            <option value="America/New_York">America/New York (EST)</option>
                                            <option value="America/Los_Angeles">America/Los Angeles (PST)</option>
                                            <option value="America/Chicago">America/Chicago (CST)</option>
                                            <option value="Europe/London">Europe/London (GMT)</option>
                                            <option value="Europe/Paris">Europe/Paris (CET)</option>
                                            <option value="Australia/Sydney">Australia/Sydney (AEST)</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Website Display -->
                                <h6 class="fw-bold mt-4 mb-3">
                                    <i class="fa-solid fa-display me-2 text-info"></i>
                                    Website Display
                                </h6>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch" id="showEmergencyPhone" v-model="business.show_emergency_phone" true-value="1" false-value="0" />
                                            <label class="form-check-label fw-semibold" for="showEmergencyPhone">Show Emergency Phone on Navigation Bar</label>
                                        </div>
                                        <div class="form-text small">Display your business phone number with an "Emergency?" label in the public website navigation.</div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end mt-4">
                                    <button type="submit" class="btn btn-info text-white rounded-4 px-4 py-2 fw-semibold btn-gradient" :disabled="saving.business">
                                        <span v-if="saving.business" class="spinner-border spinner-border-sm me-2"></span>
                                        <i v-else class="fa-solid fa-check me-2"></i>
                                        Save Business Settings
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ═══════════════ Danger Zone ═══════════════ -->
            <div v-if="activeTab === 'danger'">
                <div class="panel-card rounded-4" style="border: 1px solid rgba(220, 53, 69, 0.2) !important">
                    <div class="p-3 p-md-4">
                        <h5 class="fw-bold text-danger mb-1">
                            <i class="fa-solid fa-triangle-exclamation me-2"></i>
                            Danger Zone
                        </h5>
                        <p class="text-secondary small mb-4">Irreversible actions that affect your account or data.</p>

                        <div class="d-flex flex-column gap-3">
                            <!-- Clear all notifications -->
                            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 p-3 rounded-4 bg-light">
                                <div>
                                    <h6 class="fw-bold mb-1">Clear All Notifications</h6>
                                    <p class="text-secondary small mb-0">Permanently delete all your notifications. This cannot be undone.</p>
                                </div>
                                <button class="btn btn-outline-danger rounded-4 px-3 py-2 fw-semibold flex-shrink-0" @click="clearAllNotifications" :disabled="saving.clearNotifications">
                                    <span v-if="saving.clearNotifications" class="spinner-border spinner-border-sm me-2"></span>
                                    <i v-else class="fa-solid fa-bell-slash me-2"></i>
                                    Clear Notifications
                                </button>
                            </div>

                            <!-- Reset Business Settings -->
                            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 p-3 rounded-4 bg-light">
                                <div>
                                    <h6 class="fw-bold mb-1">Reset Business Settings</h6>
                                    <p class="text-secondary small mb-0">Restore all business settings to their default values.</p>
                                </div>
                                <button class="btn btn-outline-danger rounded-4 px-3 py-2 fw-semibold flex-shrink-0" @click="resetBusinessSettings" :disabled="saving.resetBusiness">
                                    <span v-if="saving.resetBusiness" class="spinner-border spinner-border-sm me-2"></span>
                                    <i v-else class="fa-solid fa-rotate-left me-2"></i>
                                    Reset Defaults
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Danger Confirm Modal -->
        <div class="modal fade" ref="dangerModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content border-0 rounded-4 shadow-lg">
                    <div class="modal-body text-center p-4">
                        <div class="mb-3">
                            <i class="fa-solid fa-triangle-exclamation fa-3x text-danger"></i>
                        </div>
                        <h5 class="fw-bold mb-2">{{ dangerAction?.title }}</h5>
                        <p class="text-secondary mb-0">{{ dangerAction?.message }}</p>
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4 pt-0 justify-content-center gap-2">
                        <button type="button" class="btn btn-light border rounded-4 px-4 py-2 fw-semibold" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger rounded-4 px-4 py-2 fw-semibold" @click="executeDangerAction">
                            <i class="fa-solid fa-check me-2"></i>
                            Confirm
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
            activeTab: "profile",
            role: "staff",
            allTabs: [
                { label: "Profile", value: "profile", icon: "fa-solid fa-user", adminOnly: false },
                { label: "Password", value: "password", icon: "fa-solid fa-lock", adminOnly: false },
                { label: "Business", value: "business", icon: "fa-solid fa-store", adminOnly: true },
                { label: "Danger Zone", value: "danger", icon: "fa-solid fa-triangle-exclamation", adminOnly: true },
            ],
            profile: { name: "", email: "" },
            passwordForm: { current_password: "", password: "", password_confirmation: "" },
            showPasswords: { current: false, new: false, confirm: false },
            business: {
                business_name: "",
                app_name_first: "JNJ",
                app_name_last: "Auto",
                business_email: "",
                business_phone: "",
                business_address: "",
                opening_time: "07:00",
                closing_time: "17:00",
                slot_duration: 30,
                slot_capacity: 2,
                currency: "PHP",
                timezone: "Asia/Manila",
                show_emergency_phone: "1",
                business_logo_url: null,
            },
            logoFile: null,
            logoPreview: null,
            saving: {
                profile: false,
                password: false,
                business: false,
                clearNotifications: false,
                resetBusiness: false,
            },
            toastMessage: "",
            toastClass: "text-bg-success",
            toastIcon: "fa-solid fa-circle-check",
            dangerAction: null,
        };
    },

    computed: {
        isAdmin() {
            return this.role === "admin";
        },
        tabs() {
            return this.allTabs.filter((tab) => !tab.adminOnly || this.isAdmin);
        },
        passwordValid() {
            return this.passwordForm.current_password.length > 0 && this.passwordForm.password.length >= 8 && this.passwordForm.password === this.passwordForm.password_confirmation;
        },
        passwordStrengthLabel() {
            const p = this.passwordForm.password;
            if (!p) return "Enter a password";
            if (p.length < 8) return "Too short (min 8 characters)";
            let strength = 0;
            if (/[a-z]/.test(p)) strength++;
            if (/[A-Z]/.test(p)) strength++;
            if (/[0-9]/.test(p)) strength++;
            if (/[^a-zA-Z0-9]/.test(p)) strength++;
            if (p.length >= 12) strength++;
            if (strength <= 2) return "Weak";
            if (strength <= 3) return "Fair";
            return "Strong";
        },
        passwordStrengthColor() {
            const label = this.passwordStrengthLabel;
            if (label === "Strong") return "text-success";
            if (label === "Fair") return "text-warning";
            if (label === "Weak") return "text-danger";
            return "text-secondary";
        },
    },

    mounted() {
        this.fetchSettings();
    },

    methods: {
        async fetchSettings() {
            this.loading = true;
            try {
                const { data } = await axios.get("/panel/api/settings");
                this.profile = data.profile;
                this.role = data.role || "staff";
                if (data.business) {
                    Object.assign(this.business, data.business);
                }
            } catch (e) {
                console.error("Failed to load settings", e);
            } finally {
                this.loading = false;
            }
        },

        async saveProfile() {
            this.saving.profile = true;
            try {
                const { data } = await axios.put("/panel/api/settings/profile", this.profile);
                this.showToast(data.message, "success");
            } catch (e) {
                this.showToast(e.response?.data?.message || "Failed to update profile.", "danger");
            } finally {
                this.saving.profile = false;
            }
        },

        async savePassword() {
            this.saving.password = true;
            try {
                const { data } = await axios.put("/panel/api/settings/password", this.passwordForm);
                this.showToast(data.message, "success");
                this.passwordForm = { current_password: "", password: "", password_confirmation: "" };
            } catch (e) {
                this.showToast(e.response?.data?.message || "Failed to update password.", "danger");
            } finally {
                this.saving.password = false;
            }
        },

        async saveBusinessSettings() {
            this.saving.business = true;
            try {
                const formData = new FormData();
                formData.append("_method", "PUT");

                const keys = ["business_name", "app_name_first", "app_name_last", "business_email", "business_phone", "business_address", "opening_time", "closing_time", "slot_duration", "slot_capacity", "currency", "timezone", "show_emergency_phone"];
                keys.forEach((key) => {
                    if (this.business[key] !== undefined && this.business[key] !== null) {
                        formData.append(key, this.business[key]);
                    }
                });

                if (this.logoFile) {
                    formData.append("business_logo", this.logoFile);
                }

                const { data } = await axios.post("/panel/api/settings/business", formData, {
                    headers: { "Content-Type": "multipart/form-data" },
                });
                this.logoFile = null;
                this.logoPreview = null;
                this.showToast(data.message, "success");
                // Refresh to get the updated logo URL
                await this.fetchSettings();
            } catch (e) {
                this.showToast(e.response?.data?.message || "Failed to update settings.", "danger");
            } finally {
                this.saving.business = false;
            }
        },

        onLogoSelected(event) {
            const file = event.target.files[0];
            if (!file) return;
            if (file.size > 2 * 1024 * 1024) {
                this.showToast("Logo file must be under 2MB.", "danger");
                return;
            }
            this.logoFile = file;
            this.logoPreview = URL.createObjectURL(file);
        },

        async removeLogo() {
            try {
                if (this.business.business_logo_url) {
                    await axios.delete("/panel/api/settings/business/logo");
                }
                this.logoFile = null;
                this.logoPreview = null;
                this.business.business_logo_url = null;
                if (this.$refs.logoInput) this.$refs.logoInput.value = "";
                this.showToast("Logo removed.", "success");
            } catch (e) {
                this.showToast("Failed to remove logo.", "danger");
            }
        },

        async clearAllNotifications() {
            this.dangerAction = {
                title: "Clear All Notifications",
                message: "Are you sure you want to delete ALL notifications? This cannot be undone.",
                callback: async () => {
                    this.saving.clearNotifications = true;
                    try {
                        await axios.post("/panel/api/notifications/mark-all-read");
                        await axios.post("/panel/api/notifications/clear-read");
                        this.showToast("All notifications cleared.", "success");
                    } catch (e) {
                        this.showToast("Failed to clear notifications.", "danger");
                    } finally {
                        this.saving.clearNotifications = false;
                    }
                },
            };
            this.getDangerModal().show();
        },

        async resetBusinessSettings() {
            this.dangerAction = {
                title: "Reset Business Settings",
                message: "Are you sure you want to reset all business settings to defaults?",
                callback: async () => {
                    this.saving.resetBusiness = true;
                    try {
                        // Remove logo first
                        if (this.business.business_logo_url) {
                            await axios.delete("/panel/api/settings/business/logo");
                        }
                        const defaults = {
                            business_name: "JNJ Auto Car Wash",
                            app_name_first: "JNJ",
                            app_name_last: "Auto",
                            business_email: "info@jnjauto.com",
                            business_phone: "(555) 123-4567",
                            business_address: "123 Main Street, Manila, Philippines",
                            opening_time: "07:00",
                            closing_time: "17:00",
                            slot_duration: "30",
                            slot_capacity: "2",
                            currency: "PHP",
                            timezone: "Asia/Manila",
                            show_emergency_phone: "1",
                        };
                        const { data } = await axios.put("/panel/api/settings/business", defaults);
                        Object.assign(this.business, defaults);
                        this.business.business_logo_url = null;
                        this.logoFile = null;
                        this.logoPreview = null;
                        this.showToast("Business settings reset to defaults.", "success");
                    } catch (e) {
                        this.showToast("Failed to reset settings.", "danger");
                    } finally {
                        this.saving.resetBusiness = false;
                    }
                },
            };
            this.getDangerModal().show();
        },

        async executeDangerAction() {
            if (this.dangerAction?.callback) {
                this.getDangerModal().hide();
                await this.dangerAction.callback();
            }
        },

        getDangerModal() {
            if (!this._dangerModal) {
                this._dangerModal = new bootstrap.Modal(this.$refs.dangerModal);
            }
            return this._dangerModal;
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
