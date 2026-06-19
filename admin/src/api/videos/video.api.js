import axiosInstance from "../axios";
import API_ROUTES from "../api.config";

const videoApi = {
    createVideo: async (payload) => {
        const res = await axiosInstance.post(API_ROUTES.ME.CREATE_VIDEO, payload, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });

        return {
            success: res.success,
            message: res.message,
            data: res.data,
        };
    },

    uploadVideo: async (payload) => {
        const res = await axiosInstance.post(API_ROUTES.ME.UPLOAD_VIDEO, payload, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });

        return {
            success: res.success,
            message: res.message,
            data: res.data,
        };
    },

    getVideos: async (payload) => {
        const res = await axiosInstance.get(API_ROUTES.ADMIN.GET_VIDEOS, {
            params: {
                page: payload.page,
                limit: payload.limit,
                ...(payload.status && { status: payload.status }), // Only append when `status` has value
                ...(payload.categoryId && { categoryId: payload.categoryId }), // Only append when `categoryId` has value
            },
        });

        return {
            success: res.success,
            message: res.message,
            data: res.data,
        };
    },
};

export default videoApi;
