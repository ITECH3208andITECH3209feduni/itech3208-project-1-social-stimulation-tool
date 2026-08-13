import axiosInstance from "../axios";
import API_ROUTES from "../api.config";

const categoryApi = {
    getCategories: async () => {
        const response = await axiosInstance.get(API_ROUTES.PUBLIC.CATEGORIES);
        return {
            success: response.success,
            message: response.message,
            data: response.data,
        };
    },

    createCategory: async (payload) => {
        const response = await axiosInstance.post(API_ROUTES.ADMIN.MANAGE_CATEGORIES, payload);
        return {
            success: response.success,
            message: response.message,
            data: response.data,
        };
    },

    deleteCategory: async (id) => {
        const response = await axiosInstance.delete(API_ROUTES.ADMIN.DELETE_CATEGORY(id));
        return {
            success: response.success,
            message: response.message,
            data: response.data,
        };
    },

    updateCategory: async (id, payload) => {
        const response = await axiosInstance.put(API_ROUTES.ADMIN.UPDATE_CATEGORY(id), payload);
        return {
            success: response.success,
            message: response.message,
            data: response.data,
        };
    },
};

export default categoryApi;
