import { userApi } from "@/api";
import { useQuery } from "@tanstack/react-query";

const useUserProfile = () => {
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
    });
};

export default useUserProfile;
