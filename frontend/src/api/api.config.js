const API_ROUTES = {
    // MARK: - PUBLIC (No Auth Required)
    PUBLIC: {
        VIDEOS: "/videos",
        VIDEO_DETAIL: (id) => `/videos/${id}`,
        CATEGORIES: "/categories",
        LEVELS: "/levels",
        FEEDBACKS: (videoId) => `/feedbacks/${videoId}`,
        TOP_FEEDBACKS: "/feedbacks/top-feedbacks",
    },

    // MARK: - AUTH
    AUTH: {
        LOGIN: "/auth/login-user",
        REGISTER: "/auth/register-user",
    },

    // MARK: - ME (Authenticated Users - Common)
    ME: {
        GET_INFO: "/me/get-user-infor",
        UPLOAD_AVATAR: "/me/upload-avatar",
        FEEDBACK: "/feedbacks", // POST feedback
    },

    // MARK: - INDIVIDUAL (Individual Role Only)
    INDIVIDUAL: {
        WISHLIST_TOGGLE: (videoId) => `/individual/wishlists/toggle/${videoId}`,
        WISHLIST_GET_ALL: "/individual/wishlists",
    },

    // MARK: - CONTACT (Common Authenticated)
    CONTACT: "/contacts",
};

export default API_ROUTES;
