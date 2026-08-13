import videoApi from "@/api/videos/video.api";

/**
 * A centralized query factory for video-related TanStack Query configurations.
 * Keeping queryKey and queryFn in one place prevents duplication between
 * hooks (useVideo) and prefetch calls.
 */
export const videoQueries = {
    /**
     * Query config for fetching a paginated list of videos.
     * @param {Object} params
     * @param {number} params.page - Current page number
     * @param {number} params.limit - Number of items per page
     */
    list: ({ page, limit, status = "", categoryId = "", subCategoryId = "" }) => ({
        queryKey: ["videos", { page, limit, status, categoryId, subCategoryId }],
        queryFn: async () => {
            const payload = {
                page,
                limit,
                status,
                categoryId,
                subCategoryId,
            };
            const res = await videoApi.getVideos(payload);
            if (!res.success) {
                throw new Error(res.message || "Failed to fetch videos");
            }
            return res.data;
        },
        staleTime: 30 * 1000,
        refetchInterval: 60 * 1000,
    }),
};
