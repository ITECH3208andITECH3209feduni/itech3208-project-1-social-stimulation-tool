import envConfig from "./env.config.js";

const apiConfig = {
    admin: envConfig.apiPrefix + "/admin",
    auth: envConfig.apiPrefix + "/auth",
    manageCategory: envConfig.apiPrefix + "/admin/categories",
    category: envConfig.apiPrefix + "/categories",
    level: envConfig.apiPrefix + "/levels",
    manageVideo: envConfig.apiPrefix + "/admin/videos",
    video: envConfig.apiPrefix + "/videos",
    feedback: envConfig.apiPrefix + "/feedbacks",
    user: envConfig.apiPrefix + "/users",
};

export default apiConfig;
