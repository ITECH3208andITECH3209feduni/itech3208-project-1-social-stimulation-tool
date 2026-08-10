import axiosInstance from "../axios";
import API_ROUTES from "../api.config";

const feedbackApi = {
    // GET /admin/feedbacks — paginated list with optional isPinned filter
    getAllFeedbacks: async ({ page = 1, limit = 9, isPinned, rating = 5 } = {}) => {
        const params = { page, limit, rating };
        if (isPinned !== undefined) params.isPinned = isPinned;
        const res = await axiosInstance.get(API_ROUTES.ADMIN.GET_ALL_FEEDBACKS, { params });
        return { success: res.success, message: res.message, data: res.data };
    },

    // PATCH /admin/feedbacks/:id/pin — toggle isPinned
    togglePin: async (id) => {
        const res = await axiosInstance.patch(API_ROUTES.ADMIN.TOGGLE_PIN_FEEDBACK(id));
        return { success: res.success, message: res.message, data: res.data };
    },

    // DELETE /admin/feedbacks/:id
    deleteFeedback: async (id) => {
        const res = await axiosInstance.delete(API_ROUTES.ADMIN.DELETE_FEEDBACK(id));
        return { success: res.success, message: res.message, data: res.data };
    },
};

export default feedbackApi;
