import authApi from "../api/authApi";

export default {
  async register(data) {
    const response = await authApi.register(data);

    return response.data;
  },

  async login(data) {
    const response = await authApi.login(data);

    localStorage.setItem("token", response.data.token);

    localStorage.setItem("user", JSON.stringify(response.data.user));

    return response.data;
  },

  async forgotPassword(data) {
    const response = await authApi.forgotPassword(data);

    return response.data;
  },

  async resetPassword(data) {
    const response = await authApi.resetPassword(data);

    return response.data;
  },

  async logout() {
    const response = await authApi.logout();

    localStorage.removeItem("token");

    localStorage.removeItem("user");

    return response.data;
  },
};
