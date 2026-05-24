import { authApi } from "@/api";

const useRegister = () => {
    const register = async (payload, callback = {}) => {
        try {
            const res = await authApi.register(payload);
            if (res.success) {
                callback.onSuccess(res.data, res.message);
            }
        } catch (error) {
            callback.onError?.(error.message);
        } finally {
            callback.onFinally?.();
        }
    };

    return { register };
};

export default useRegister;
