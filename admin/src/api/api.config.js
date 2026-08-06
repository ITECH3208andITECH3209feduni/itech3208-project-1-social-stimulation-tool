const API_ROUTES = {
    // MARK: - AUTH
    AUTH: {
        LOGIN: "/auth/login-user",
    },

    // MARK: - PUBLIC (No Auth Required)
    PUBLIC: {
        CATEGORIES: "/categories",
        SUBCATEGORIES: "/subcategories",
    },

    // MARK: - ADMIN
    ADMIN: {
        GET_VIDEOS: "/admin/videos",
        GET_ALL_USERS: "/admin/users",
        GET_All_CONTACTS: "/admin/contacts",
        UPDATE_USER_STATUS: (id) => `/admin/users/${id}/account-status`,
        DELETE_USER: (id) => `/admin/users/${id}`,
        MANAGE_CATEGORIES: "/admin/categories",
        UPDATE_CATEGORY: (id) => `/admin/categories/${id}`,
        DELETE_CATEGORY: (id) => `/admin/categories/${id}`,
        MANAGE_SUBCATEGORIES: "/admin/subcategories",
        UPDATE_SUBCATEGORY: (id) => `/admin/subcategories/${id}`,
        DELETE_SUBCATEGORY: (id) => `/admin/subcategories/${id}`,
    },

    VIDEOS: {
        UPLOAD_VIDEO: "/videos",
        GET_ALL: "/videos",
        DELETE_VIDEO: (id) => `/admin/videos/${id}`,
        UPDATE_VIDEO: (id) => `/admin/videos/${id}`,
    },

    ME: {
        CREATE_VIDEO: "/me/videos",
        UPLOAD_VIDEO: "/me/videos/upload-video",
    },
};

export default API_ROUTES;
