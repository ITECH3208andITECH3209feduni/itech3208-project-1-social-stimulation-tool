import { authApi } from "@/api";
import useAuthStore from "../stores/useAuthStore";

const useLogin = () => {
    const setAuth = useAuthStore((state) => state.setAuth);

    const login = async (payload, callback = {}) => {
        try {
            const res = await authApi.login({
                username: payload.username,
                password: payload.password,
            });
            if (res.success) {
                callback.onSuccess?.(res.data, res.message);
                console.log(res.data.accessToken)
                setAuth(res.data.accessToken);
            }
        } catch (error) {
            callback.onError?.(error.message);
        } finally {
            callback.onFinally?.();
        }
    };

    return { login };
};

export default useLogin;
