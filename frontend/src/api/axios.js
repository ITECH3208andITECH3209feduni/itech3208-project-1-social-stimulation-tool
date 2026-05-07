import axios from "axios";
import useAuthStore from "@/stores/useAuthStore";

const axiosInstance = axios.create({
    baseURL: import.meta.env.VITE_API_BASE_URL,
    timeout: 10000,
    headers: {
        "Content-Type": "application/json",
    },
});

// MARK: - REQUEST INTERCEPTOR
axiosInstance.interceptors.request.use(
    (config) => {
        const { accessToken } = useAuthStore.getState();
        if (accessToken) {
            config.headers.Authorization = `Bearer ${accessToken}`;
        }
        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

// MARK: - RESPONSE INTERCEPTOR
axiosInstance.interceptors.response.use(
    (response) => {
        return response.data; // Simplify response data access
    },
    (error) => {
        if (error.response?.status === 401) {
            // Unauthorized - clear auth state if token is invalid
            useAuthStore.getState().clearAuth();
        }
        
        // Normalize error response
        const message = error.response?.data?.message || error.message || "Something went wrong";
        return Promise.reject({
            ...error,
            message,
        });
    }
);

export default axiosInstance;
