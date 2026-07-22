import api from "./axios";

export default {
  /**
   * Fetch paginated audit logs.
   *
   * @param {Object} params
   * @param {string}  [params.search]       - free-text search (action / user)
   * @param {string}  [params.action]       - exact action filter
   * @param {string}  [params.target_table] - module filter
   * @param {string}  [params.date_from]    - ISO date lower bound
   * @param {string}  [params.date_to]      - ISO date upper bound
   * @param {number}  [params.page]
   * @param {number}  [params.per_page]
   */
  getAll(params = {}) {
    return api.get("/audit-logs", { params });
  },

  /** Fetch the N most-recent logs (used on the dashboard). */
  getRecent(limit = 5) {
    return api.get("/audit-logs", { params: { page: 1, per_page: limit } });
  },

  /** Fetch distinct action values for the filter dropdown. */
  getActions() {
    return api.get("/audit-logs/actions");
  },

  /** Fetch logs for a specific user. */
  getUserLogs(userId, params = {}) {
    return api.get(`/audit-logs/user/${userId}`, { params });
  },
};
