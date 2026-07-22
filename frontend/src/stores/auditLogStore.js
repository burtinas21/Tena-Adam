import { defineStore } from "pinia";
import auditLogApi from "../api/auditLogApi";

export const useAuditLogStore = defineStore("auditLog", {
  state: () => ({
    logs: [],
    meta: {
      current_page: 1,
      last_page: 1,
      per_page: 5,
      total: 0,
      from: 0,
      to: 0,
    },
    availableActions: [],

    filters: {
      search: "",
      action: "",
      target_table: "",
      date_from: "",
      date_to: "",
    },

    loading: false,
    error: null,
    errorStatus: null, // HTTP status code of the error (403, 500, etc.)
  }),

  getters: {
    totalPages:  (state) => state.meta.last_page,
    currentPage: (state) => state.meta.current_page,
    hasLogs:     (state) => state.logs.length > 0,
    isForbidden: (state) => state.errorStatus === 403,
  },

  actions: {
    async fetchLogs(page = 1) {
      this.loading     = true;
      this.error       = null;
      this.errorStatus = null;

      try {
        const params = {
          page,
          per_page: this.meta.per_page,
          // only include non-empty filter values
          ...Object.fromEntries(
            Object.entries(this.filters).filter(([, v]) => v !== "")
          ),
        };

        const res  = await auditLogApi.getAll(params);
        const body = res.data;

        // Laravel resource collections wrap data in "data" key
        // and pagination in "meta" key
        this.logs = body.data ?? [];

        this.meta = {
          current_page: body.meta?.current_page ?? body.current_page ?? 1,
          last_page:    body.meta?.last_page    ?? body.last_page    ?? 1,
          per_page:     body.meta?.per_page     ?? body.per_page     ?? 20,
          total:        body.meta?.total        ?? body.total        ?? 0,
          from:         body.meta?.from         ?? body.from         ?? 0,
          to:           body.meta?.to           ?? body.to           ?? 0,
        };
      } catch (err) {
        this.errorStatus = err.response?.status ?? null;
        if (this.errorStatus === 403) {
          this.error = "You do not have permission to view audit logs.";
        } else if (this.errorStatus === 401) {
          this.error = "You must be logged in to view audit logs.";
        } else {
          this.error =
            err.response?.data?.message ?? err.message ?? "Failed to load audit logs.";
        }
        console.error("[AuditLogStore] fetchLogs error:", this.errorStatus, this.error);
      } finally {
        this.loading = false;
      }
    },

    async fetchActions() {
      try {
        const res              = await auditLogApi.getActions();
        this.availableActions  = res.data?.data ?? [];
      } catch (err) {
        // non-critical – dropdown just stays empty, log for debugging
        console.warn("[AuditLogStore] fetchActions failed:", err.response?.status);
      }
    },

    async applyFilters(patch = {}) {
      this.filters = { ...this.filters, ...patch };
      await this.fetchLogs(1);
    },

    async resetFilters() {
      this.filters = {
        search:       "",
        action:       "",
        target_table: "",
        date_from:    "",
        date_to:      "",
      };
      await this.fetchLogs(1);
    },

    async goToPage(page) {
      if (page < 1 || page > this.meta.last_page) return;
      await this.fetchLogs(page);
    },

    async setPerPage(perPage) {
      this.meta.per_page = perPage;
      await this.fetchLogs(1);
    },
  },
});
