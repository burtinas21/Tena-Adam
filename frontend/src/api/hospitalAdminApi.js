import api from "./axios";

export default {
  getAll() {
    return api.get("/hospital-staff");
  },
  create(data) {
    return api.post("/hospital-staff", data);
  },
  update(id, data) {
    return api.put(`/hospital-staff/${id}`, data);
  },
  destroy(id) {
    return api.delete(`/hospital-staff/${id}`);
  },
};
