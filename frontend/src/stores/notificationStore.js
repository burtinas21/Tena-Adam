import { defineStore } from "pinia";
import notificationApi from "../api/notificationApi";

export const useNotificationStore = defineStore("notification", {
  state: () => ({
    notifications: [],
    unreadCount: 0,
    loading: false,
    error: null,
    preferences: null,
    prefsLoading: false,
  }),

  getters: {
    unread: (state) =>
      state.notifications.filter((n) => n.status !== "read"),
    byChannel: (state) => (channel) =>
      state.notifications.filter((n) => n.channel === channel),
  },

  actions: {
    // ── Notifications ───────────────────────────────────────────────────
    async fetchAll(unreadOnly = false) {
      try {
        this.loading = true;
        this.error = null;
        const res = await notificationApi.getAll(unreadOnly);
        // NotificationResource::collection wraps in { data: [...] }
        const raw = res.data?.data ?? res.data ?? [];
        const arr = Array.isArray(raw) ? raw : [];
        // Always show newest notifications first
        this.notifications = arr.slice().sort(
          (a, b) => new Date(b.created_at) - new Date(a.created_at)
        );
        // Sync unread count from fetched data
        this.unreadCount = this.notifications.filter((n) => n.status !== "read").length;
      } catch (err) {
        const status = err.response?.status;
        if (status === 403) {
          this.error = "You do not have permission to view notifications.";
        } else if (status === 401) {
          this.error = "Session expired. Please log in again.";
        } else {
          this.error = err.response?.data?.message || "Failed to load notifications";
        }
      } finally {
        this.loading = false;
      }
    },

    async fetchUnreadCount() {
      try {
        const res = await notificationApi.getUnreadCount();
        this.unreadCount = res.data?.count ?? 0;
      } catch {
        // silent — badge failure shouldn't break anything
      }
    },

    async markAsRead(id) {
      try {
        await notificationApi.markAsRead(id);
        const n = this.notifications.find((n) => n.id === id);
        if (n) n.status = "read";
        if (this.unreadCount > 0) this.unreadCount--;
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to mark as read";
        throw err;
      }
    },

    async markAllRead() {
      try {
        await notificationApi.markAllRead();
        this.notifications.forEach((n) => (n.status = "read"));
        this.unreadCount = 0;
      } catch (err) {
        this.error =
          err.response?.data?.message || "Failed to mark all as read";
        throw err;
      }
    },

    async destroy(id) {
      try {
        await notificationApi.destroy(id);
        this.notifications = this.notifications.filter((n) => n.id !== id);
        this.unreadCount = this.notifications.filter(
          (n) => n.status !== "read"
        ).length;
      } catch (err) {
        this.error =
          err.response?.data?.message || "Failed to delete notification";
        throw err;
      }
    },

    async retry(id) {
      try {
        const res = await notificationApi.retry(id);
        const updated = res.data?.data ?? res.data;
        const idx = this.notifications.findIndex((n) => n.id === id);
        if (idx !== -1) this.notifications[idx] = updated;
        return updated;
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to retry";
        throw err;
      }
    },

    // Admin: send manual notification
    async send(data) {
      try {
        this.loading = true;
        const res = await notificationApi.send(data);
        return res.data?.data ?? res.data;
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to send notification";
        throw err;
      } finally {
        this.loading = false;
      }
    },

    // ── Preferences ─────────────────────────────────────────────────────
    async fetchPreferences() {
      try {
        this.prefsLoading = true;
        const res = await notificationApi.getPreferences();
        this.preferences = res.data?.data ?? res.data;
      } catch (err) {
        this.error =
          err.response?.data?.message || "Failed to load preferences";
      } finally {
        this.prefsLoading = false;
      }
    },

    async savePreferences(data) {
      try {
        this.prefsLoading = true;
        const res = await notificationApi.updatePreferences(data);
        this.preferences = res.data?.data ?? res.data;
        return this.preferences;
      } catch (err) {
        this.error =
          err.response?.data?.message || "Failed to save preferences";
        throw err;
      } finally {
        this.prefsLoading = false;
      }
    },
  },
});
