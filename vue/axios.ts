import axios from "axios";

const axiosClient = axios.create({
  baseURL: useRuntimeConfig().public.BASE_URL + '/api',
});
// axiosClient.defaults.headers.post['Access-Control-Allow-Origin'] = '*';
export default axiosClient;
