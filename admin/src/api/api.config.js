const API_ROUTES = {
    // MARK: - AUTH
    AUTH: {
        LOGIN: "/auth/login-user",
    },

    // MARK: - PUBLIC (No Auth Required)
    PUBLIC: {
        CATEGORIES: "/categories",
    },

    // MARK: - ADMIN
    ADMIN: {
        GET_VIDEOS: "/videos",
    },

    ME: {
        CREATE_VIDEO: "/me/videos",
        UPLOAD_VIDEO: "/me/videos/upload-video",
    },

    // MARK: - Contacts
    CONTACTS: {
        GET_All_CONTACTS: "/contacts",
    },
};

export default API_ROUTES;
