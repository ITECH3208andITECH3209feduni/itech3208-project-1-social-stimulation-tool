import axiosInstance from "../axios";
import API_ROUTES from "../api.config";
import useAuthStore from "@/hooks/stores/useAuthStore";

const authApi = {
    login: async (credentials) => {
        const response = await axiosInstance.post(API_ROUTES.AUTH.LOGIN, credentials);
        const { data } = response;
        
        useAuthStore.getState().setAuth({
            user: data.user,
            accessToken: data.accessToken,
        });
        
        return response;
    },

    register: async (userData) => {
        return await axiosInstance.post(API_ROUTES.AUTH.REGISTER, userData);
    },

    logout: () => {
        useAuthStore.getState().clearAuth();
    },
};

export default authApi;
