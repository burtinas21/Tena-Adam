import axios from "./axios";

export default {
  // Payments
  create(data) {
    return axios.post("/payments", data);
  },

  getAll(params = {}) {
    return axios.get("/payments", { params });
  },

  getById(id) {
    return axios.get(`/payments/${id}`);
  },

  update(id, data) {
    return axios.put(`/payments/${id}`, data);
  },

  delete(id) {
    return axios.delete(`/payments/${id}`);
  },

  // Invoices
  getInvoices() {
    return axios.get("/invoices");
  },

  getInvoice(id) {
    return axios.get(`/invoices/${id}`);
  },

  downloadInvoice(id) {
    return axios.get(`/invoices/${id}/download`, {
      responseType: "blob",
    });
  },

  // Refunds
  getRefunds() {
    return axios.get("/refunds");
  },

  createRefund(data) {
    return axios.post("/refunds", data);
  },

  getRefund(id) {
    return axios.get(`/refunds/${id}`);
  },

  approveRefund(id) {
    return axios.patch(`/refunds/${id}/approve`);
  },

  processRefund(id) {
    return axios.patch(`/refunds/${id}/process`);
  },
};
