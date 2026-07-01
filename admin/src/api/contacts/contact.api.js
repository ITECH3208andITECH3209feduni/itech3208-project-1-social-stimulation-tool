import axiosInstance from "../axios";
import API_ROUTES from "../api.config";

const contactApi = {
    getAllContacts: async (payload) => {
        const response = await axiosInstance.get(API_ROUTES.CONTACTS.GET_All_CONTACTS, {
            params: {
                page: payload.page,
                limit: payload.limit,
            },
        });

        return {
            success: response.success,
            message: response.message,
            data: response.data,
        };
    },
};

export default contactApi;
