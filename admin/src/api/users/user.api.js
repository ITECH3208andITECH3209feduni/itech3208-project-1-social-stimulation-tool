import axiosInstance from "../axios";
import API_ROUTES from "../api.config";

const userApi = {
    getUserInfo: async () => {
        const response = await axiosInstance.get(API_ROUTES.ME.GET_INFO);
        return {
            success: response.success,
            message: response.message,
            data: response.data,
        };
    },

    uploadAvatar: async (formData) => {
        const response = await axiosInstance.patch(API_ROUTES.ME.UPLOAD_AVATAR, formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });

        return {
            success: response.success,
            message: response.message,
            data: response.data,
        };
    },

    updateUserProfile: async (payload) => {
        const response = await axiosInstance.patch(API_ROUTES.ME.UPDATE_PROFILE, payload);
        return {
            success: response.success,
            message: response.message,
            data: response.data,
        };
    },

    getAllUsers: async (payload) => {
        const response = await axiosInstance.get(API_ROUTES.ADMIN.GET_ALL_USERS, {
            params: {
                page: payload.page,
                limit: payload.limit,
                ...(payload.role && { role: payload.role }),
            },
        });

        return {
            success: response.success,
            message: response.message,
            data: response.data,
        };
    },
};

export default userApi;
