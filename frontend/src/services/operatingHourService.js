import operatingHourApi from "../api/operatingHourApi";

export default {
  async getAll() {
    const response = await operatingHourApi.getAll();
    return response.data;
  },
  async create(data) {
    const response = await operatingHourApi.create(data);
    return response.data;
  },
  async update(id, data) {
    const response = await operatingHourApi.update(id, data);
    return response.data;
  },
  async destroy(id) {
    const response = await operatingHourApi.destroy(id);
    return response.data;
  },
};
