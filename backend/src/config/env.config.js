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
    accessTokenSecretKey: process.env.ACCESS_TOKEN_SECRET,
    refreshTokenSecretKey: process.env.REFRESH_TOKEN_SECRET,
    cloudinaryName: process.env.CLOUDINARY_NAME,
    cloudinaryApiKey: process.env.CLOUDINARY_API_KEY,
    cloudinaryApiSecret: process.env.CLOUDINARY_API_SECRET_KEY,
};

export default envConfig;
