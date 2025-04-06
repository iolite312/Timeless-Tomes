import axios from "axios";

const axiosClient = axios.create({
  baseURL: useRuntimeConfig().public.BASE_URL + '/api',
});

axiosClient.interceptors.response.use(
  response => response,
  error => {
    if (error.response?.status === 422) {
      return Promise.reject(error.response.data);
    }
    console.error('API error:', error.response?.data || error.message);
    throw createError({
      statusCode: error.response?.status || 500,
      statusMessage: error.response?.data?.message || 'Something went wrong',
      fatal: true,
    });
  }
);

export default axiosClient;
