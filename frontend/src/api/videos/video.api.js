import axiosInstance from "../axios";
import API_ROUTES from "../api.config";

const videoApi = {
    // PUBLIC
    getVideos: async (params) => {
        return await axiosInstance.get(API_ROUTES.PUBLIC.VIDEOS, { params });
    },

    getVideoDetail: async (id) => {
        return await axiosInstance.get(API_ROUTES.PUBLIC.VIDEO_DETAIL(id));
    },
};

export default videoApi;
