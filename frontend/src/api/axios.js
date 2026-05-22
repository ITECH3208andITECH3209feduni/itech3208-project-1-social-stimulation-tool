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
    },
);

// MARK: - RESPONSE INTERCEPTOR
const extractErrorStatus = (error) => {
    return error?.response?.status || 500;
};

const extractErrorMessage = (error) => {
    const status = extractErrorStatus(error);
    const data = error?.response?.data;

    const possibleMessages = [data?.message, data?.details?.[0]?.message, error?.message];

    return status == 500 ? possibleMessages[0] : possibleMessages[1];
};

axiosInstance.interceptors.response.use(
    (response) => {
        return response.data;
    },
    (error) => {
        const status = extractErrorStatus(error);
        const message = extractErrorMessage(error);

        loggerUtil.error(`[AxiosResponseError] [${status}]`, {
            message,
        });

        if (status === 401) {
            useAuthStore.getState().clearAuth();
        }

        return Promise.reject({
            ...error,
            status,
            message,
        });
    },
);

export default axiosInstance;
