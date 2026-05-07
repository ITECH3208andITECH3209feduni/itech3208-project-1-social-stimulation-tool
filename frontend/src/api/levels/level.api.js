import axiosInstance from "../axios";
import API_ROUTES from "../api.config";

const levelApi = {
    getLevels: async () => {
        return await axiosInstance.get(API_ROUTES.PUBLIC.LEVELS);
    },
};

export default levelApi;
