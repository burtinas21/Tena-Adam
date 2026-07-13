import hospitalAdminApi from "../api/hospitalAdminApi";

export default {
  async getAll() {
    const response = await hospitalAdminApi.getAll();
    return response.data;
  },
  async create(data) {
    const response = await hospitalAdminApi.create(data);
    return response.data;
  },
};
