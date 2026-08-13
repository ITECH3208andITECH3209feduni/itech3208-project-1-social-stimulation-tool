import userApi from "./user.api";

/**
 * A centralized query factory for user-related TanStack Query configurations.
 * Keeping queryKey and queryFn in one place prevents duplication between
 * hooks (useUser) and prefetch calls.
 */
export const userQueries = {
    /**
     * Query config for fetching a paginated list of videos.
     * @param {Object} params
     * @param {number} params.page - Current page number
     * @param {number} params.limit - Number of items per page
     * @param {number} params.role - Role for user
     */
    list: ({ page, limit, role = "" }) => ({
        queryKey: ["users", { page, limit, role }],
        queryFn: async () => {
            const payload = {
                page: page,
                limit: limit,
                role: "",
            };
            const res = await userApi.getAllUsers(payload);
            if (!res.success) {
                throw new Error(res.message || "Failed to fetch videos");
            }
            return res.data;
        },
        // Data is considered fresh for 30 seconds — no refetch within this window
        staleTime: 30 * 1000,
        // Automatically refetch in the background every 60 seconds
        refetchInterval: 60 * 1000,
    }),
};
