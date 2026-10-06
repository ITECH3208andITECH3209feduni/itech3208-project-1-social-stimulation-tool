import axiosInstance from "../axios";
import API_ROUTES from "../api.config";

const videoApi = {
    getVideos: async (payload = {}) => {
        const response = await axiosInstance.get(API_ROUTES.PUBLIC.VIDEOS, {
            params: {
                page: payload.page,
                limit: payload.limit,
                ...(payload.status && { status: payload.status }),
                ...(payload.categoryId && { categoryId: payload.categoryId }),
                ...(payload.subCategoryId && { subCategoryId: payload.subCategoryId }),
            },
        });

        return {
            success: response.success,
            message: response.message,
            data: response.data,
        };
    },

    getVideoDetail: async (id) => {
        const response = await axiosInstance.get(API_ROUTES.PUBLIC.VIDEO_DETAIL(id));

        return {
            success: response.success,
            message: response.message,
            data: response.data,
        };
    },
};

export default videoApi;
