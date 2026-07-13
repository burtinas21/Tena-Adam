import facilityApi from "../api/facilityApi";

export default {
  async getAll() {
    const response = await facilityApi.getAll();
    return response.data;
  },
  async create(data) {
    const response = await facilityApi.create(data);
    return response.data;
  },
  async update(id, data) {
    const response = await facilityApi.update(id, data);
    return response.data;
  },
  async destroy(id) {
    const response = await facilityApi.destroy(id);
    return response.data;
  },
};
