import axios from "axios";

const api = axios.create({
  baseURL: "http://127.0.0.1:8000/api",
  headers: {
    "Content-Type": "application/json",
    Accept: "application/json",
  },
});

api.interceptors.request.use(
  (config) => {
    // Auth token
    const token = localStorage.getItem("token");
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }

    // Send current language so the backend can use it for dynamic data
    const lang = localStorage.getItem("language") || "en";
    config.headers["Accept-Language"] = lang;

    return config;
  },
  (error) => Promise.reject(error),
);

export default api;
