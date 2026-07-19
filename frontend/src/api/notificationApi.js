import api from "./axios";

export default {
  // ── Notifications ─────────────────────────────────────────────────────
  getAll(unreadOnly = false) {
    return api.get("/notifications", { params: unreadOnly ? { unread: true } : {} });
  },

  getUnreadCount() {
    return api.get("/notifications/unread-count");
  },

  getById(id) {
    return api.get(`/notifications/${id}`);
  },

  markAsRead(id) {
    return api.patch(`/notifications/${id}`, { status: "read" });
  },

  markAllRead() {
    return api.patch("/notifications/mark-all-read");
  },

  retry(id) {
    return api.patch(`/notifications/${id}/retry`);
  },

  destroy(id) {
    return api.delete(`/notifications/${id}`);
  },

  // Admin only — send a manual notification
  send(data) {
    return api.post("/notifications", data);
  },

  // ── User Preferences ──────────────────────────────────────────────────
  getPreferences() {
    return api.get("/notification-preferences");
  },

  updatePreferences(data) {
    return api.put("/notification-preferences", data);
  },

  // ── Notification Templates (platform_admin only) ──────────────────────
  getTemplates() {
    return api.get("/notification-templates");
  },

  getTemplate(id) {
    return api.get(`/notification-templates/${id}`);
  },

  createTemplate(data) {
    return api.post("/notification-templates", data);
  },

  updateTemplate(id, data) {
    return api.put(`/notification-templates/${id}`, data);
  },

  destroyTemplate(id) {
    return api.delete(`/notification-templates/${id}`);
  },
};
