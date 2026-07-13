import api from "./axios";

export default {
  // GET all schedules (returns all; filter by doctor in store)
  getAll() {
    return api.get("/doctor-schedules");
  },
  getById(id) {
    return api.get(`/doctor-schedules/${id}`);
  },
  create(data) {
    return api.post("/doctor-schedules", data);
  },
  update(id, data) {
    return api.put(`/doctor-schedules/${id}`, data);
  },
  destroy(id) {
    return api.delete(`/doctor-schedules/${id}`);
  },
};
