import axiosInstance from "../axios";
import API_ROUTES from "../api.config";

const subCategoryApi = {
    getSubCategories: async (params = {}) => {
        const response = await axiosInstance.get(API_ROUTES.PUBLIC.SUBCATEGORIES, { params });
        return {
            success: response.success,
            message: response.message,
            data: response.data,
        };
    },

    createSubCategory: async (payload) => {
        const response = await axiosInstance.post(API_ROUTES.ADMIN.MANAGE_SUBCATEGORIES, payload);
        return {
            success: response.success,
            message: response.message,
            data: response.data,
        };
    },

    updateSubCategory: async (id, payload) => {
        const response = await axiosInstance.put(API_ROUTES.ADMIN.UPDATE_SUBCATEGORY(id), payload);
        return {
            success: response.success,
            message: response.message,
            data: response.data,
        };
    },

    deleteSubCategory: async (id) => {
        const response = await axiosInstance.delete(API_ROUTES.ADMIN.DELETE_SUBCATEGORY(id));
        return {
            success: response.success,
            message: response.message,
            data: response.data,
        };
    },
};

export default subCategoryApi;
