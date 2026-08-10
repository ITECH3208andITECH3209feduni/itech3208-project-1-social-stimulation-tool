import feedbackApi from "./feedback.api";

export const feedbackQueries = {
    /**
     * Paginated admin list of all feedbacks.
     * @param {number} page
     * @param {number} limit
     * @param {boolean|undefined} isPinned - filter by pin status, undefined = all
     */
    list: ({ page, limit, isPinned, rating }) => ({
        queryKey: ["feedbacks", { page, limit, isPinned, rating }],
        queryFn: async () => {
            const res = await feedbackApi.getAllFeedbacks({ page, limit, isPinned, rating });
            if (!res.success) throw new Error(res.message || "Failed to fetch feedbacks");
            return res.data;
        },
        staleTime: 30 * 1000,
    }),
};
