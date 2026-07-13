import api from "./axios";

export default {
  register(data) {
    return api.post("/register", data);
  },

  login(data) {
    return api.post("/login", data);
  },

  forgotPassword(data) {
    return api.post("/forgot-password", data);
  },

  resetPassword(data) {
    return api.post("/reset-password", data);
  },

  logout() {
    return api.post("/logout");
  },
};
