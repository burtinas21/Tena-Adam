import api from "./axios";

export default {
  getAll() {
    return api.get("/facilities");
  },
  getById(id) {
    return api.get(`/facilities/${id}`);
  },
  create(data) {
    return api.post("/facilities", data);
  },
  update(id, data) {
    return api.put(`/facilities/${id}`, data);
  },
  destroy(id) {
    return api.delete(`/facilities/${id}`);
  },
};
