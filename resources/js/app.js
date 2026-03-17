import "./bootstrap";
import { createApp } from "vue";

const app = createApp({});

import Overlay from "./components/Overlay.vue";
app.component("overlay", Overlay);

import Appointment from "./components/Appointment.vue";
app.component("appointment-component", Appointment);

import PanelDashboard from "./components/PanelDashboard.vue";
app.component("panel-dashboard", PanelDashboard);

import PanelAppointments from "./components/PanelAppointments.vue";
app.component("panel-appointments", PanelAppointments);

import PanelServices from "./components/PanelServices.vue";
app.component("panel-services", PanelServices);

import PanelSales from "./components/PanelSales.vue";
app.component("panel-sales", PanelSales);

import PanelInventory from "./components/PanelInventory.vue";
app.component("panel-inventory", PanelInventory);

import PanelNotifications from "./components/PanelNotifications.vue";
app.component("panel-notifications", PanelNotifications);

import NotificationBell from "./components/NotificationBell.vue";
app.component("notification-bell", NotificationBell);

import PanelSettings from "./components/PanelSettings.vue";
app.component("panel-settings", PanelSettings);

import PanelUsers from "./components/PanelUsers.vue";
app.component("panel-users", PanelUsers);

app.mount("#app");
