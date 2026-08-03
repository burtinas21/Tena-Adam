import { defineStore } from "pinia";
import queueApi from "../api/queueApi";

export const useQueueStore = defineStore("queue", {
  state: () => ({
    entries:  [],   // all queue entries for the current doctor/date
    loading:  false,
    error:    null,
    actionLoading: false,   // for call-next / complete / skip / recall
  }),

  getters: {
    waiting:        (s) => s.entries.filter((e) => e.status === "waiting"),
    inConsultation: (s) => s.entries.filter((e) => e.status === "in_consultation"),
    completed:      (s) => s.entries.filter((e) => e.status === "completed"),
    skipped:        (s) => s.entries.filter((e) => e.status === "skipped"),
    noShow:         (s) => s.entries.filter((e) => e.status === "no_show"),

    /** The single patient currently in consultation (or null). */
    currentPatient: (s) =>
      s.entries.find((e) => e.status === "in_consultation") ?? null,

    totalWaiting:   (s) => s.entries.filter((e) => e.status === "waiting").length,
    totalCompleted: (s) => s.entries.filter((e) => e.status === "completed").length,
  },

  actions: {
    /** Load queue for a doctor on a given date. */
    async fetchQueue(doctorId, date) {
      try {
        this.loading = true;
        this.error   = null;
        const res    = await queueApi.getDoctorQueue(doctorId, date);
        this.entries = res.data?.data ?? res.data ?? [];
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to load queue.";
        throw err;
      } finally {
        this.loading = false;
      }
    },

    /** Add a walk-in patient to the queue. */
    async addWalkIn(data) {
      try {
        this.actionLoading = true;
        this.error = null;
        const res    = await queueApi.generate(data);
        const entry  = res.data?.data ?? res.data;
        // Insert in order: priority DESC, then queue_number ASC
        this.entries.push(entry);
        this.entries.sort((a, b) =>
          b.priority !== a.priority
            ? b.priority - a.priority
            : a.queue_number - b.queue_number
        );
        return entry;
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to add walk-in.";
        throw err;
      } finally {
        this.actionLoading = false;
      }
    },

    /** Call the next waiting patient. */
    async callNext(doctorId, date) {
      try {
        this.actionLoading = true;
        this.error = null;
        const res  = await queueApi.callNext(doctorId, date);
        // Re-fetch to get the freshest state
        await this.fetchQueue(doctorId, date);
        return res.data;
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to call next patient.";
        throw err;
      } finally {
        this.actionLoading = false;
      }
    },

    /** Complete the consultation for a queue entry. */
    async complete(queueId, doctorId, date) {
      try {
        this.actionLoading = true;
        this.error = null;
        await queueApi.complete(queueId);
        await this.fetchQueue(doctorId, date);
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to complete consultation.";
        throw err;
      } finally {
        this.actionLoading = false;
      }
    },

    /** Skip a patient (moves them to end of queue). */
    async skip(queueId, doctorId, date) {
      try {
        this.actionLoading = true;
        this.error = null;
        await queueApi.skip(queueId);
        await this.fetchQueue(doctorId, date);
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to skip patient.";
        throw err;
      } finally {
        this.actionLoading = false;
      }
    },

    /** Recall a skipped patient back to waiting. */
    async recall(queueId, doctorId, date) {
      try {
        this.actionLoading = true;
        this.error = null;
        await queueApi.recall(queueId);
        await this.fetchQueue(doctorId, date);
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to recall patient.";
        throw err;
      } finally {
        this.actionLoading = false;
      }
    },

    /**
     * Apply a real-time queue update received via WebSocket broadcast.
     * Updates the matching entry in-place, or inserts if new.
     */
    applyBroadcast(updatedEntry) {
      const idx = this.entries.findIndex((e) => e.id === updatedEntry.id);
      if (idx !== -1) {
        this.entries[idx] = { ...this.entries[idx], ...updatedEntry };
      } else {
        this.entries.push(updatedEntry);
        this.entries.sort((a, b) =>
          b.priority !== a.priority
            ? b.priority - a.priority
            : a.queue_number - b.queue_number
        );
      }
    },
  },
});
