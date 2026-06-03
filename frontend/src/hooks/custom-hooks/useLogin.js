import { authApi } from "@/api";
import useAuthStore from "../stores/useAuthStore";
import { useQueryClient } from "@tanstack/react-query";

const useLogin = () => {
    const setAuth = useAuthStore((state) => state.setAuth);
    const queryClient = useQueryClient();

    const login = async (payload, callback = {}) => {
        try {
            const res = await authApi.login({
                username: payload.username,
                password: payload.password,
            });
            if (res.success) {
                setAuth(res.data.accessToken);
                queryClient.invalidateQueries({
                    queryKey: ["user-profile"],
                });
                callback.onSuccess?.(res.data, res.message);
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
