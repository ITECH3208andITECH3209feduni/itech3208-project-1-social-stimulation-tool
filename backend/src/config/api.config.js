import envConfig from "./env.config.js";

const apiPrefix = envConfig.apiPrefix;

const apiConfig = {
    // MARK: - AUTH
    auth: apiPrefix + "/auth",

    // MARK: - PUBLIC
    video: apiPrefix + "/videos",
    feedback: apiPrefix + "/feedbacks",
    category: apiPrefix + "/categories",
    level: apiPrefix + "/levels",

    // MARK: - COMMON (Individual & Organization)
    me: apiPrefix + "/me",

    // MARK: - INDIVIDUAL ONLY
    individual: apiPrefix + "/individual",

    // MARK: - ORGANIZATION ONLY
    organization: apiPrefix + "/organization",

    // MARK: - ADMIN ONLY
    admin: apiPrefix + "/admin",
    manageVideo: apiPrefix + "/admin/videos",
    manageCategory: apiPrefix + "/admin/categories",
};

export default apiConfig;
