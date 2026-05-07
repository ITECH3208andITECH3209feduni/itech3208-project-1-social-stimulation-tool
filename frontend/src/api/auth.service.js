import axiosInstance from "./axios";
import API_ROUTES from "./api.config";
import useAuthStore from "@/stores/useAuthStore";

const AuthService = {
    login: async (credentials) => {
        try {
            const response = await axiosInstance.post(API_ROUTES.AUTH.LOGIN, credentials);
            const { data } = response;
            
            // Save to store
            useAuthStore.getState().setAuth({
                user: data.user,
                accessToken: data.accessToken,
            });
            
            return response;
        } catch (error) {
            throw error;
        }
    },

    register: async (userData) => {
        try {
            const response = await axiosInstance.post(API_ROUTES.AUTH.REGISTER, userData);
            return response;
        } catch (error) {
            throw error;
        }
    },

    logout: () => {
        useAuthStore.getState().clearAuth();
    },
};

export default AuthService;
