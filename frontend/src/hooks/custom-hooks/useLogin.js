import { authApi } from "@/api";
import useAuthStore from "../stores/useAuthStore";
import { useQueryClient } from "@tanstack/react-query";
import isUserAccessToken from "@/utils/isUserAccessToken";

const useLogin = () => {
    const setAuth = useAuthStore((state) => state.setAuth);
    const queryClient = useQueryClient();
    const [isLoading, setIsLoading] = useState(false);

    const login = async (payload, callback = {}) => {
        setIsLoading(true);
        try {
            const res = await authApi.login({
                username: payload.username,
                password: payload.password,
            });
            if (res.success) {
                if (!isUserAccessToken(res.data.accessToken)) {
                    callback.onError?.("This account must sign in through the admin portal.");
                    return;
                }

                setAuth(res.data.accessToken);
                queryClient.invalidateQueries({
                    queryKey: ["user-profile"],
                });
                callback.onSuccess?.(res.data, res.message);
            }
        } catch (error) {
            callback.onError?.(error.message);
        } finally {
            setIsLoading(false);
            callback.onFinally?.();
        }
    };

    return { login, isLoading };
};

export default useLogin;
