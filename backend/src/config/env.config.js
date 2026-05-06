import dotenv from "dotenv";

dotenv.config({
    path: ".env.dev",
});

const envConfig = {
    nodeEnv: process.env.NODE_ENV,
    apiPrefix: process.env.API_PREFIX,
    port: process.env.PORT,
    mongoUrl: process.env.MONGODB_URL,
    mongoApiUrl: process.env.MONGODB_API_URL,
    atlasPublicKey: process.env.ATLAS_PUBLIC_KEY,
    atlasPrivateKey: process.env.ATLAS_PRIVATE_KEY,
    atlasProjectId: process.env.ATLAS_PROJECT_ID,
    accessToken: process.env.ACCESS_TOKEN_SECRET,
    refreshToken: process.env.REFRESH_TOKEN_SECRET,
};

export default envConfig;
