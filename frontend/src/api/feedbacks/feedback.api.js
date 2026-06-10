import axiosInstance from "../axios";
import API_ROUTES from "../api.config";

const feedbackApi = {
    // PUBLIC
    getFeedbacksByVideo: async (videoId, params) => {
        return await axiosInstance.get(API_ROUTES.PUBLIC.FEEDBACKS(videoId), { params });
    },

    getTopFeedbacks: async () => {
        return await axiosInstance.get(API_ROUTES.PUBLIC.TOP_FEEDBACKS);
    },

    // AUTHENTICATED
    createFeedback: async (payload) => {
        const response = await axiosInstance.post(API_ROUTES.ME.FEEDBACK, payload);
        return {
            success: response.success,
            message: response.message,
            data: response.data,
        };
    },

    dismissFeedbackPopup: async () => {
        const response = await axiosInstance.post(API_ROUTES.ME.DISMISS_FEEDBACK_POPUP);
        return {
            success: response.success,
            message: response.message,
            data: response.data,
        };
    },
};

export default feedbackApi;
