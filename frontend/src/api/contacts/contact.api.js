import axiosInstance from "../axios";
import API_ROUTES from "../api.config";

const contactApi = {
    sendContact: async (payload) => {
        return await axiosInstance.post(API_ROUTES.CONTACT, payload);
    },
};

export default contactApi;
