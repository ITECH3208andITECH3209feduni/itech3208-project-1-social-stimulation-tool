import axiosInstance from "../axios";
import API_ROUTES from "../api.config";

const userApi = {
    getUserInfo: async () => {
        return await axiosInstance.get(API_ROUTES.ME.GET_INFO);
    },

    uploadAvatar: async (formData) => {
        return await axiosInstance.patch(API_ROUTES.ME.UPLOAD_AVATAR, formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });
    },
};

export default userApi;
