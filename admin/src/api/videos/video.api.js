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
};

export default videoApi;
