import { userApi } from "@/api";
import { useQuery } from "@tanstack/react-query";
import useAuthStore from "@/hooks/stores/useAuthStore";
import isUserAccessToken from "@/utils/isUserAccessToken";

const useUserProfile = () => {
    const accessToken = useAuthStore((state) => state.accessToken);

    return useQuery({
        queryKey: ["user-profile"],
        queryFn: async () => {
            const res = await userApi.getUserInfo();

            if (!res.success) {
                throw new Error(res.message || "Failed to fetch user profile");
            }

            return res.data;
        },
        staleTime: 1000 * 60 * 5, // 5 minutes cache
        gcTime: 1000 * 60 * 10, // optional (v5)
        retry: 1,
        refetchOnWindowFocus: false,
        enabled: isUserAccessToken(accessToken),
    });
};

export default useUserProfile;
