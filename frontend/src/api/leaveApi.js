import api from "./axios";

export default {
  getAll() {
    return api.get("/doctor-leaves");
  },
  getById(id) {
    return api.get(`/doctor-leaves/${id}`);
  },
  create(data) {
    return api.post("/doctor-leaves", data);
  },
  update(id, data) {
    return api.put(`/doctor-leaves/${id}`, data);
  },
  destroy(id) {
    return api.delete(`/doctor-leaves/${id}`);
  },
  approve(id, status) {
    return api.patch(`/doctor-leaves/${id}/approve`, { status });
  },
};
