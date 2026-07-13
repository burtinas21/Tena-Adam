import api from "./axios";

export default {
  getAll() {
    return api.get("/hospitals");
  },
  getById(id) {
    return api.get(`/hospitals/${id}`);
  },
  create(data) {
    return api.post("/hospitals", data);
  },
  update(id, data) {
    return api.put(`/hospitals/${id}`, data);
  },
  destroy(id) {
    return api.delete(`/hospitals/${id}`);
  },
};
