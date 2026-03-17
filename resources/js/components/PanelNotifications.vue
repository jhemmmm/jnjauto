<template>
    <div>
        <!-- Filter tabs & actions -->
        <div class="panel-card rounded-4 mb-4">
            <div class="p-3 p-md-4">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                    <div class="d-flex gap-2 flex-wrap">
                        <button
                            v-for="tab in tabs"
                            :key="tab.value"
                            class="btn rounded-4 px-3 py-2 fw-semibold"
                            :class="filter === tab.value ? 'btn-info text-white btn-gradient' : 'btn-light border'"
                            @click="
                                filter = tab.value;
                                fetchNotifications();
                            ">
                            <i :class="tab.icon" class="me-1"></i>
                            {{ tab.label }}
                            <span v-if="tab.value === 'unread' && stats.unread > 0" class="badge text-bg-danger rounded-pill ms-1">{{ stats.unread }}</span>
                        </button>
                    </div>

                    <div class="d-flex gap-2">
                        <button class="btn btn-light border rounded-4 px-3 py-2 fw-semibold" @click="markAllRead" :disabled="stats.unread === 0">
                            <i class="fa-solid fa-check-double me-2"></i>
                            Mark All Read
                        </button>
                        <button class="btn btn-light border rounded-4 px-3 py-2 fw-semibold text-danger" @click="clearRead" :disabled="stats.total - stats.unread === 0">
                            <i class="fa-solid fa-trash me-2"></i>
                            Clear Read
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loading -->
        <div v-if="loading" class="text-center py-5">
            <div class="spinner-border text-info" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>

        <!-- Empty state -->
        <div v-else-if="notifications.length === 0" class="panel-card rounded-4">
            <div class="p-5 text-center">
                <div class="mb-3">
                    <i class="fa-regular fa-bell-slash fa-3x text-secondary opacity-25"></i>
                </div>
                <h5 class="fw-bold text-secondary">No notifications</h5>
                <p class="text-secondary mb-0">
                    <span v-if="filter === 'unread'">You're all caught up! No unread notifications.</span>
                    <span v-else-if="filter === 'read'">No read notifications found.</span>
                    <span v-else>Notifications will appear here when events happen in your system.</span>
                </p>
            </div>
        </div>

        <!-- Notification list -->
        <div v-else class="d-flex flex-column gap-2">
            <div v-for="n in notifications" :key="n.id" class="panel-card rounded-4 notification-card" :class="{ 'notification-unread': !n.read_at }">
                <div class="p-3 p-md-4">
                    <div class="d-flex gap-3">
                        <!-- Icon -->
                        <div class="notification-icon rounded-4 d-flex align-items-center justify-content-center flex-shrink-0" :class="'notification-icon-' + (n.icon_color || 'secondary')">
                            <i :class="n.icon || 'fa-solid fa-bell'"></i>
                        </div>

                        <!-- Content -->
                        <div class="flex-grow-1 min-w-0">
                            <div class="d-flex align-items-start justify-content-between gap-2">
                                <div>
                                    <h6 class="fw-bold mb-1">
                                        {{ n.title }}
                                        <span v-if="!n.read_at" class="pulse-dot ms-2"></span>
                                    </h6>
                                    <p class="text-secondary mb-1 notification-message">{{ n.message }}</p>
                                    <small class="text-secondary">
                                        <i class="fa-regular fa-clock me-1"></i>
                                        {{ timeAgo(n.created_at) }}
                                    </small>
                                </div>

                                <!-- Actions -->
                                <div class="dropdown flex-shrink-0">
                                    <button class="btn btn-sm btn-light border rounded-3" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fa-solid fa-ellipsis-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end rounded-4 shadow border-0">
                                        <li v-if="!n.read_at">
                                            <button class="dropdown-item rounded-3" @click="markRead(n)">
                                                <i class="fa-solid fa-check me-2 text-success"></i>
                                                Mark as Read
                                            </button>
                                        </li>
                                        <li v-if="n.link">
                                            <a :href="n.link" class="dropdown-item rounded-3">
                                                <i class="fa-solid fa-arrow-right me-2 text-info"></i>
                                                View Details
                                            </a>
                                        </li>
                                        <li>
                                            <button class="dropdown-item rounded-3 text-danger" @click="deleteNotification(n)">
                                                <i class="fa-solid fa-trash me-2"></i>
                                                Delete
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="pagination.lastPage > 1" class="d-flex justify-content-center mt-3">
                <nav>
                    <ul class="pagination mb-0">
                        <li class="page-item" :class="{ disabled: pagination.currentPage === 1 }">
                            <button class="page-link rounded-start-4" @click="goToPage(pagination.currentPage - 1)">
                                <i class="fa-solid fa-chevron-left"></i>
                            </button>
                        </li>
                        <li v-for="page in paginationRange" :key="page" class="page-item" :class="{ active: page === pagination.currentPage }">
                            <button class="page-link" @click="goToPage(page)">{{ page }}</button>
                        </li>
                        <li class="page-item" :class="{ disabled: pagination.currentPage === pagination.lastPage }">
                            <button class="page-link rounded-end-4" @click="goToPage(pagination.currentPage + 1)">
                                <i class="fa-solid fa-chevron-right"></i>
                            </button>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            notifications: [],
            loading: true,
            filter: "all", // all | unread | read
            stats: { total: 0, unread: 0 },
            pagination: {
                currentPage: 1,
                lastPage: 1,
                total: 0,
            },
            tabs: [
                { label: "All", value: "all", icon: "fa-solid fa-inbox" },
                { label: "Unread", value: "unread", icon: "fa-solid fa-envelope" },
                { label: "Read", value: "read", icon: "fa-solid fa-envelope-open" },
            ],
            pollInterval: null,
        };
    },

    computed: {
        paginationRange() {
            const current = this.pagination.currentPage;
            const last = this.pagination.lastPage;
            const range = [];
            const start = Math.max(1, current - 2);
            const end = Math.min(last, current + 2);
            for (let i = start; i <= end; i++) {
                range.push(i);
            }
            return range;
        },
    },

    mounted() {
        this.fetchNotifications();
        // Real-time polling every 15 seconds
        this.pollInterval = setInterval(() => {
            this.fetchNotifications(true);
        }, 15000);
    },

    beforeUnmount() {
        if (this.pollInterval) {
            clearInterval(this.pollInterval);
        }
    },

    methods: {
        async fetchNotifications(silent = false) {
            if (!silent) this.loading = true;
            try {
                const params = new URLSearchParams();
                if (this.filter !== "all") params.append("filter", this.filter);
                params.append("page", this.pagination.currentPage);

                const { data } = await axios.get(`/panel/api/notifications?${params}`);
                this.notifications = data.notifications.data;
                this.pagination.currentPage = data.notifications.current_page;
                this.pagination.lastPage = data.notifications.last_page;
                this.pagination.total = data.notifications.total;
                this.stats = data.stats;
            } catch (e) {
                console.error("Failed to load notifications", e);
            } finally {
                this.loading = false;
            }
        },

        async markRead(notification) {
            try {
                await axios.patch(`/panel/api/notifications/${notification.id}/read`);
                notification.read_at = new Date().toISOString();
                this.stats.unread = Math.max(0, this.stats.unread - 1);
            } catch (e) {
                console.error("Failed to mark notification as read", e);
            }
        },

        async markAllRead() {
            try {
                await axios.post("/panel/api/notifications/mark-all-read");
                this.notifications.forEach((n) => {
                    if (!n.read_at) n.read_at = new Date().toISOString();
                });
                this.stats.unread = 0;
            } catch (e) {
                console.error("Failed to mark all as read", e);
            }
        },

        async deleteNotification(notification) {
            try {
                await axios.delete(`/panel/api/notifications/${notification.id}`);
                this.notifications = this.notifications.filter((n) => n.id !== notification.id);
                if (!notification.read_at) {
                    this.stats.unread = Math.max(0, this.stats.unread - 1);
                }
                this.stats.total = Math.max(0, this.stats.total - 1);
            } catch (e) {
                console.error("Failed to delete notification", e);
            }
        },

        async clearRead() {
            try {
                await axios.post("/panel/api/notifications/clear-read");
                if (this.filter === "read") {
                    this.notifications = [];
                } else {
                    this.notifications = this.notifications.filter((n) => !n.read_at);
                }
                this.stats.total = this.stats.unread;
                this.fetchNotifications();
            } catch (e) {
                console.error("Failed to clear read notifications", e);
            }
        },

        goToPage(page) {
            if (page < 1 || page > this.pagination.lastPage) return;
            this.pagination.currentPage = page;
            this.fetchNotifications();
        },

        timeAgo(dateStr) {
            const now = new Date();
            const date = new Date(dateStr);
            const seconds = Math.floor((now - date) / 1000);

            if (seconds < 60) return "Just now";
            const minutes = Math.floor(seconds / 60);
            if (minutes < 60) return minutes + "m ago";
            const hours = Math.floor(minutes / 60);
            if (hours < 24) return hours + "h ago";
            const days = Math.floor(hours / 24);
            if (days < 7) return days + "d ago";
            return date.toLocaleDateString("en-US", { month: "short", day: "numeric", year: "numeric" });
        },
    },
};
</script>
