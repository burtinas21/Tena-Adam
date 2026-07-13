import hospitalApi from "../api/hospitalApi";

export default {
  async getAll() {
    const response = await hospitalApi.getAll();
    return response.data;
  },
  async create(data) {
    const response = await hospitalApi.create(data);
    return response.data;
  },
  async update(id, data) {
    const response = await hospitalApi.update(id, data);
    return response.data;
  },
  async destroy(id) {
    const response = await hospitalApi.destroy(id);
    return response.data;
  },
};
