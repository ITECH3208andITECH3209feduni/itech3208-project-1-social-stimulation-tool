import axiosInstance from "../axios";
import API_ROUTES from "../api.config";
import useAuthStore from "@/hooks/stores/useAuthStore";

const authApi = {
    login: async ({ username, password }) => {
        const response = await axiosInstance.post(API_ROUTES.AUTH.LOGIN, { username, password });
        return {
            success: response.success,
            message: response.message,
            data: response.data,
        };
    },

    register: async (userData) => {
        return await axiosInstance.post(API_ROUTES.AUTH.REGISTER, userData);
    },

    logout: () => {
        useAuthStore.getState().clearAuth();
    },
};

export default authApi;
