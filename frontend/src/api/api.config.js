const API_ROUTES = {
    AUTH: {
        LOGIN: "/auth/login-user",
        REGISTER: "/auth/register-user",
        LOGOUT: "/auth/logout", // Placeholder if needed
    },
    ME: {
        GET_INFO: "/me/get-user-infor",
        UPLOAD_AVATAR: "/me/upload-avatar",
        VIDEOS: "/me/videos",
    },
    VIDEOS: {
        GET_ALL: "/videos",
        GET_DETAIL: (id) => `/videos/${id}`,
    },
    CATEGORIES: {
        GET_ALL: "/categories",
    },
    LEVELS: {
        GET_ALL: "/levels",
    },
    FEEDBACKS: {
        CREATE: "/feedbacks",
        GET_BY_VIDEO: (videoId) => `/feedbacks/${videoId}`,
        TOP: "/feedbacks/top-feedbacks",
    },
    WISHLIST: {
        TOGGLE: (videoId) => `/individual/wishlists/toggle/${videoId}`,
        GET_ALL: "/individual/wishlists",
    },
    CONTACTS: {
        SEND: "/contacts",
    },
};

export default API_ROUTES;
