import axios from "axios";
import useAuthStore from "@/hooks/stores/useAuthStore";
import loggerUtil from "@/utils/logger.utils";

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
        loggerUtil.error("[AxiosRequestError]", error);
        return Promise.reject(error);
    }
);

// MARK: - RESPONSE INTERCEPTOR
axiosInstance.interceptors.response.use(
    (response) => {
        return response.data;
    },
    (error) => {
        const status = error.response?.status;
        const message = error.response?.data?.message || error.message || "Something went wrong";

        loggerUtil.error(`[AxiosResponseError] [${status || "Network"}]`, {
            url: error.config?.url,
            method: error.config?.method,
            message,
            data: error.response?.data,
        });

        if (status === 401) {
            useAuthStore.getState().clearAuth();
        }
        
        return Promise.reject({
            ...error,
            message,
        });
    }
);

export default axiosInstance;
