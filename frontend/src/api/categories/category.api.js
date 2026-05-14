import axiosInstance from "../axios";
import API_ROUTES from "../api.config";

const categoryApi = {
    getCategories: async () => {
        return await axiosInstance.get(API_ROUTES.PUBLIC.CATEGORIES);
    },
};

export default categoryApi;
