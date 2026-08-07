import api from "./axios";

export default {
  getAll() {
    return api.get("/roles");
  },

  getById(id) {
    return api.get(`/roles/${id}`);
  },

  create(data) {
    return api.post("/roles", data);
  },

  update(id, data) {
    return api.put(`/roles/${id}`, data);
  },

  destroy(id) {
    return api.delete(`/roles/${id}`);
  },

  syncPermissions(roleId, permissionIds) {
    return api.put(`/roles/${roleId}/permissions`, {
      permission_ids: permissionIds,
    });
  },

  getUsers(roleId) {
    return api.get(`/roles/${roleId}/users`);
  },
};
