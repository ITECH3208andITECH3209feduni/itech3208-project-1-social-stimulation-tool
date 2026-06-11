import axiosInstance from "../axios";
import API_ROUTES from "../api.config";

const categoryApi = {
    getCategories: async () => {
        const response = await axiosInstance.get(API_ROUTES.PUBLIC.CATEGORIES);
        return {
            success: response.success,
            message: response.message,
            data: response.data
        }
    },
};

export default categoryApi;
