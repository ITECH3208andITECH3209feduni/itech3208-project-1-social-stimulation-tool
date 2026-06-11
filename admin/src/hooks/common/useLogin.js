import { authApi } from "@/api";
import useAuthStore from "../stores/useAuthStore";
import { useState } from "react";

const useLogin = () => {
    const setAuth = useAuthStore((state) => state.setAuth);
    const [loading, setLoading] = useState(false);

    const login = async (payload, callback = {}) => {
        try {
            setLoading(true);
            const res = await authApi.login({
                username: payload.username,
                password: payload.password,
            });
            if (res.success) {
                setAuth(res.data.accessToken);
                callback.onSuccess?.(res.data, res.message);
                setLoading(false);
            }
        } catch (error) {
            callback.onError?.(error.message);
            setLoading(false);
        } finally {
            callback.onFinally?.();
            setLoading(false);
        }
    };

    return { loading, login };
};

export default useLogin;
