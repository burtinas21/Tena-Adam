import axios from "./axios";

export default {
  getAll(language) {
    return axios.get("/translations/all", {
      params: {
        language,
      },
    });
  },

  translate(key, language) {
    return axios.get("/translations", {
      params: {
        key,
        language,
      },
    });
  },
};
