import axiosInstance from "../axios";
import API_ROUTES from "../api.config";

const contactApi = {
    sendContact: async (payload) => {
        const response = await axiosInstance.post(API_ROUTES.CONTACT, payload);
        return {
            success: response.success,
            message: response.message,
            data: response.data
        }
    },
};

export default contactApi;
