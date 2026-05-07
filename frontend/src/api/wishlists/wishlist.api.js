import axiosInstance from "../axios";
import API_ROUTES from "../api.config";

const wishlistApi = {
    toggleWishlist: async (videoId) => {
        return await axiosInstance.patch(API_ROUTES.INDIVIDUAL.WISHLIST_TOGGLE(videoId));
    },

    getWishlist: async (params) => {
        return await axiosInstance.get(API_ROUTES.INDIVIDUAL.WISHLIST_GET_ALL, { params });
    },
};

export default wishlistApi;
