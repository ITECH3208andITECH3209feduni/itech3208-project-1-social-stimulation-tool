import axiosInstance from "../axios";
import API_ROUTES from "../api.config";

const userApi = {
    getUserInfo: async () => {
        return await axiosInstance.get(API_ROUTES.ME.GET_INFO);
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
};

export default userApi;
