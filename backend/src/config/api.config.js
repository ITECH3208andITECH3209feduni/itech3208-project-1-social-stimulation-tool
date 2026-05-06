import envConfig from "./env.config.js";

const apiConfig = {
    admin: envConfig.apiPrefix + "/admin",
    auth: envConfig.apiPrefix + "/auth",
    manageCategory: envConfig.apiPrefix + "/admin/categories",
    category: envConfig.apiPrefix + "/categories",
    manageVideo: envConfig.apiPrefix + "/admin/products",
    video: envConfig.apiPrefix + "/products",
    user: envConfig.apiPrefix + "/users",
};

export default apiConfig;
