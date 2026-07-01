import contactApi from "./contact.api";

/**
 * A centralized query factory for contact-related TanStack Query configurations.
 * Keeping queryKey and queryFn in one place prevents duplication between
 * hooks (useContact) and prefetch calls.
 */
export const contactQueries = {
    /**
     * Query config for fetching a paginated list of contacts.
     * @param {Object} params
     * @param {number} params.page - Current page number
     * @param {number} params.limit - Number of items per page
     */
    list: ({ page, limit }) => ({
        queryKey: ["contacts", { page, limit }],
        queryFn: async () => {
            const payload = {
                page: page,
                limit: limit,
            };
            const res = await contactApi.getAllContacts(payload);
            if (!res.success) {
                throw new Error(res.message || "Failed to fetch contacts");
            }
            return res.data;
        },
        // Data is considered fresh for 30 seconds — no refetch within this window
        staleTime: 30 * 1000,
        // Automatically refetch in the background every 60 seconds
        refetchInterval: 60 * 1000,
    }),
};
