<template>
    <div class="notification-bell-wrapper" ref="bellWrapper">
        <!-- Bell button -->
        <button class="btn btn-light border rounded-4 px-3 py-2 position-relative" @click="toggleDropdown">
            <i class="fa-regular fa-bell"></i>
            <span v-if="unreadCount > 0" class="notification-count-badge">
                {{ unreadCount > 99 ? "99+" : unreadCount }}
            </span>
        </button>

        <!-- Dropdown -->
        <div v-if="showDropdown" class="notification-dropdown shadow-lg rounded-4 border-0">
            <div class="notification-dropdown-header d-flex align-items-center justify-content-between p-3 border-bottom">
                <h6 class="fw-bold mb-0">Notifications</h6>
                <div class="d-flex gap-2 align-items-center">
                    <button v-if="unreadCount > 0" class="btn btn-sm btn-light rounded-3 fw-semibold" @click.stop="markAllRead">
                        <i class="fa-solid fa-check-double me-1"></i>
                        Read all
                    </button>
                </div>
            </div>

            <div class="notification-dropdown-body">
                <div v-if="loading" class="text-center py-4">
                    <div class="spinner-border spinner-border-sm text-info" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>

                <div v-else-if="recentNotifications.length === 0" class="text-center py-4 px-3">
                    <i class="fa-regular fa-bell-slash fa-2x text-secondary opacity-25 mb-2"></i>
                    <p class="text-secondary small mb-0">No new notifications</p>
                </div>

                <div v-else>
                    <a v-for="n in recentNotifications" :key="n.id" :href="n.link || '#'" class="notification-dropdown-item d-flex gap-3 p-3 text-decoration-none" :class="{ 'notification-unread-item': !n.read_at }" @click="handleNotificationClick(n, $event)">
                        <div class="notification-icon notification-icon-sm rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" :class="'notification-icon-' + (n.icon_color || 'secondary')">
                            <i :class="n.icon || 'fa-solid fa-bell'" class="small"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="fw-semibold small text-dark text-truncate">{{ n.title }}</div>
                            <div class="text-secondary small text-truncate notification-dropdown-message">{{ n.message }}</div>
                            <div class="text-secondary small mt-1" style="font-size: 0.7rem">{{ timeAgo(n.created_at) }}</div>
                        </div>
                        <span v-if="!n.read_at" class="pulse-dot flex-shrink-0 mt-1"></span>
                    </a>
                </div>
            </div>

            <div class="notification-dropdown-footer p-2 border-top text-center">
                <a href="/panel/notifications" class="btn btn-sm btn-light rounded-4 fw-semibold w-100">View All Notifications</a>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            unreadCount: 0,
            recentNotifications: [],
            showDropdown: false,
            loading: false,
            pollInterval: null,
        };
    },

    mounted() {
        this.fetchUnreadCount();
        // Real-time polling every 15 seconds
        this.pollInterval = setInterval(() => {
            this.fetchUnreadCount();
        }, 15000);

        // Close dropdown on outside click
        document.addEventListener("click", this.handleOutsideClick);
    },

    beforeUnmount() {
        if (this.pollInterval) {
            clearInterval(this.pollInterval);
        }
        document.removeEventListener("click", this.handleOutsideClick);
    },

    methods: {
        async fetchUnreadCount() {
            try {
                const { data } = await axios.get("/panel/api/notifications/unread-count");
                this.unreadCount = data.count;
                this.recentNotifications = data.recent;
            } catch (e) {
                // Silently fail — user may not be authenticated
            }
        },

        toggleDropdown() {
            this.showDropdown = !this.showDropdown;
            if (this.showDropdown && this.recentNotifications.length === 0) {
                this.fetchUnreadCount();
            }
        },

        handleOutsideClick(e) {
            if (this.$refs.bellWrapper && !this.$refs.bellWrapper.contains(e.target)) {
                this.showDropdown = false;
            }
        },

        handleNotificationClick(n, event) {
            if (!n.read_at) {
                this.markRead(n);
            }
            if (!n.link) {
                event.preventDefault();
            }
        },

        async markRead(notification) {
            try {
                await axios.patch(`/panel/api/notifications/${notification.id}/read`);
                notification.read_at = new Date().toISOString();
                this.unreadCount = Math.max(0, this.unreadCount - 1);
            } catch (e) {
                console.error("Failed to mark as read", e);
            }
        },

        async markAllRead() {
            try {
                await axios.post("/panel/api/notifications/mark-all-read");
                this.recentNotifications.forEach((n) => {
                    n.read_at = new Date().toISOString();
                });
                this.unreadCount = 0;
            } catch (e) {
                console.error("Failed to mark all as read", e);
            }
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
            return date.toLocaleDateString("en-US", { month: "short", day: "numeric" });
        },
    },
};
</script>
